import { Subscription, Observable } from 'rxjs';
// Angular
import { ChangeDetectionStrategy, Component, OnDestroy, OnInit } from '@angular/core';
import { NavigationEnd, Router } from '@angular/router';
// Layout
import { LayoutConfigService, SplashScreenService, TranslationService } from './core/_base/layout';

// language list
// import { locale as enLang } from './core/_config/i18n/en';
// import { locale as chLang } from './core/_config/i18n/ch';
// import { locale as esLang } from './core/_config/i18n/es';
// import { locale as jpLang } from './core/_config/i18n/jp';
// import { locale as deLang } from './core/_config/i18n/de';
// import { locale as frLang } from './core/_config/i18n/fr';

import { Store, select } from '@ngrx/store';
import { AppState } from './core/reducers';
import { currentUser } from './core/auth';
import { MessageService } from './services/message.service';

@Component({
	// tslint:disable-next-line:component-selector
	selector: 'body[kt-root]',
	templateUrl: './app.component.html',
	styleUrls: ['./app.component.scss'],
	changeDetection: ChangeDetectionStrategy.OnPush
})
export class AppComponent implements OnInit, OnDestroy {
	// Public properties
	title = 'TOPP - ERP';
	loader: boolean;
	hide: boolean = false;
	private unsubscribe: Subscription[] = []; // Read more: => https://brianflove.com/2016/12/11/anguar-2-unsubscribe-observables/

	/**
	 * Component constructor
	 *
	 * @param translationService: TranslationService
	 * @param router: Router
	 * @param layoutConfigService: LayoutCongifService
	 * @param splashScreenService: SplashScreenService
	 */
	constructor(
		private translationService: TranslationService,
		private router: Router,
		private layoutConfigService: LayoutConfigService,
		private splashScreenService: SplashScreenService,
		private store: Store<AppState>,
		private message: MessageService,
	) {

		// register translations
		// this.translationService.loadTranslations(enLang, chLang, esLang, jpLang, deLang, frLang);
	}

	loadUser() {
		return new Observable<boolean>(observer => {
			observer.next(false);
			this.store.pipe(select(currentUser))
				.subscribe((user: any) => {
					if (user != undefined) {
						this.alertaRegistro(user.registro.dias);
						observer.next(true);
					}
				});
		})
	}

	alertaRegistro(data) {

		const days = data;

		if (days == 0 && this.hide == false) {
			this.registroWarning(`Sua licença vence <b>HOJE</b>`);
			this.hide = true;
		} else if ((days > 0 && days < 5) && this.hide == false) {
			this.registroWarning(`Sua licença está vencida hà <b>${days} dias</b>`);
			this.hide = true;
		} else if ((days > 0 && days == 5) && this.hide == false) {
			this.registroWarning(`Sua licença está vencida hà <b>${days} dias</b>! </br> O sistema será bloqueado amanhã!`);
			this.hide = true;
		} else if (days > 5) {
			this.registroDanger(`Sua licença está vencida hà <b>${days} dias</b>! </br> Efetue o pagamento para liberação do sistema!`);
		}
		console.log(days);
	}

	registroWarning(msg) {
		msg = msg + `</br></br> Entre em contato com a nossa central: </br> <b>(63)9 99632031</b>`;
		this.message.swal.fire({
			title: 'Atenção!',
			icon: 'warning',
			html: msg,
			confirmButtonText: 'OK'
		});
	}
	registroDanger(msg) {
		msg = msg + `</br></br> Entre em contato com a nossa central: </br> <b>(63)9 99632031</b>`;
		this.message.swal.fire({
			title: 'Bloqueado!',
			icon: 'error',
			html: msg,
			showConfirmButton: false,
			allowOutsideClick: false
		});
	}

	/**
	 * @ Lifecycle sequences => https://angular.io/guide/lifecycle-hooks
	 */

	/**
	 * On init
	 */
	ngOnInit(): void {
		// enable/disable loader
		this.loader = this.layoutConfigService.getConfig('loader.enabled');

		const routerSubscription = this.router.events.subscribe(event => {
			if (event instanceof NavigationEnd) {
				this.loadUser().subscribe(resp => {
					// console.log(resp);
					if (resp == true) {
						// hide splash screen
						this.splashScreenService.hide();
					}
				})

				// scroll to top on every route change
				window.scrollTo(0, 0);

				// to display back the body content
				setTimeout(() => {
					document.body.classList.add('kt-page--loaded');
				}, 500);
			}
		});

		this.unsubscribe.push(routerSubscription);

		// this.store.pipe(select(currentUser)).subscribe(user => {
		//    console.log(user);
		//    if (user != undefined) {


		//    }
		// });

	}

	/**
	 * On Destroy
	 */
	ngOnDestroy() {
		this.unsubscribe.forEach(sb => sb.unsubscribe());
	}
}

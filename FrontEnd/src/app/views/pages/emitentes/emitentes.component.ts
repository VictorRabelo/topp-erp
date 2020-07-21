import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Router } from '@angular/router';
import { Store, select } from '@ngrx/store';
import { EmitenteService } from '../../../services/emitente.service';
import { MessageService } from '../../../services/message.service';
import { EmitenteFormComponent } from './emitente-form/emitente-form.component';

import { AppState } from '../../../core/reducers';
import { currentUser } from '../../../core/auth';
import { FiscalSendXmlComponent } from '../modais/fiscal-send-xml/fiscal-send-xml.component';



@Component({
	selector: 'kt-emitentes',
	templateUrl: './emitentes.component.html',
	styleUrls: ['./emitentes.component.scss']
})
export class EmitentesComponent implements OnInit {

	loading = false;

	dataSource = [];

	plano: number = 1;
	permissions: any = {};

	constructor(
		private ref: ChangeDetectorRef,
		private message: MessageService,
		private service: EmitenteService,
		private modalCtrl: NgbModal,
		private router: Router,
		private store: Store<AppState>
	) {
		this.load_list();

		this.getRegistro();
	}

	ngOnInit() {
	}

	getRegistro() {
		this.store.pipe(select(currentUser)).subscribe((resp: any) => {
			if (resp) {
				this.plano = resp.registro.plano;
				this.permissions = resp.permissions;
				console.log(this.permissions);
			}
		});
	}

	load_list() {
		this.loading = true;
		this.service.getList({}).subscribe(resp => {
			this.loading = false;
			this.dataSource = resp;
			this.ref.detectChanges();
		}, erro => {
			this.ref.detectChanges();
			console.log(erro);
			this.loading = false;
		});
	}

	add() {
		const modalRef = this.modalCtrl.open(EmitenteFormComponent, { size: 'lg', backdrop: 'static' });

		modalRef.result.then(resp => {
			if (resp != undefined) {
				this.load_list();
			}
		});
	}

	edit(item) {
		// const modalRef = this.modalCtrl.open(EmitenteFormComponent, { size: 'lg', backdrop: 'static' });
		// modalRef.componentInstance.data = item;
		// modalRef.result.then(resp => {
		//    if (resp != undefined) {
		//       this.load_list();
		//    }
		// });
		this.router.navigate([`/emitente/${item.id}`]);
	}

	delete_item(item) {
		this.message.swal.fire({
			title: 'Excluir ?',
			icon: 'question',
			html: `Desaja excluir o emitente: <br><b>${item.razao}</b> ?`,
			confirmButtonText: 'Confirmar',
			showCancelButton: true,
			cancelButtonText: 'Não',
		}).then((result) => {
			if (!result.dismiss) {
				this.delete(item);
				// console.log(result);
			}
		});
	}

	delete(item) {
		this.loading = true;
		this.service.delete(item.id).subscribe(resp => {
			this.loading = false;
			this.load_list();
		}, erro => {
			this.loading = false;
		});
	}

	sendXML(item) {
		const modalRef = this.modalCtrl.open(FiscalSendXmlComponent, { size: 'md', backdrop: 'static' });
		modalRef.componentInstance.emitente = item;
	}

}

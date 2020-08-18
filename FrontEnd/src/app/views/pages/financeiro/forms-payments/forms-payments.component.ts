import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Store, select } from '@ngrx/store';
import { AppState } from '../../../../core/reducers';
import { FinanceiroService } from '../../../../services/financeiro.service';
import { MessageService } from '../../../../services/message.service';
import { currentUser } from '../../../../core/auth';
import { PaymentFormComponent } from './payment-form/payment-form.component';

@Component({
	selector: 'kt-forms-payments',
	templateUrl: './forms-payments.component.html',
	styleUrls: ['./forms-payments.component.scss']
})
export class FormsPaymentsComponent implements OnInit {

	loading = false;

	dataSource = [];

	filters: any = {};
	permissions: any = {};

	constructor(
		private ref: ChangeDetectorRef,
		private message: MessageService,
		private service: FinanceiroService,
		private modalCtrl: NgbModal,
		private store: Store<AppState>,
	) { }

	ngOnInit() {
		this.getPermissions();
		this.load_list();
	}

	getPermissions() {
		this.store.pipe(select(currentUser)).subscribe((resp: any) => {
			if (resp) {
				console.log(resp.permissions);
				this.permissions = resp.permissions;
			}
		});
	}

	load_list() {
		this.loading = true;
		this.service.getListPayments(this.filters).subscribe(resp => {
			this.loading = false;
			this.dataSource = resp;
			this.ref.detectChanges();
		}, erro => {
			this.loading = false;
			this.ref.detectChanges();
		});
	}

	add() {
		const modalRef = this.modalCtrl.open(PaymentFormComponent, { size: 'md', backdrop: 'static' });

		modalRef.result.then(resp => {
			if (resp != undefined) {
				this.load_list();
			}
		});
	}

	edit(item) {
		const modalRef = this.modalCtrl.open(PaymentFormComponent, { size: 'md', backdrop: 'static' });
		modalRef.componentInstance.data = item;
		modalRef.result.then(resp => {
			if (resp != undefined) {
				this.load_list();
			}
		});

	}

	delete_item(item) {
		this.message.swal.fire({
			title: 'Excluir ?',
			icon: 'question',
			html: `Desaja excluir o cadastro: <br><b>${item.razao}</b> ?`,
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
		this.service.deletePayments(item.id).subscribe(resp => {
			this.loading = false;
			this.load_list();
		}, erro => {
			this.loading = false;
		});
	}

	open_filters(content) {
		this.modalCtrl.open(content, { size: 'md', backdrop: 'static' });
	}

}

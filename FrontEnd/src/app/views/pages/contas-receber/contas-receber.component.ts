import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Store, select } from '@ngrx/store';
import { AppState } from '../../../core/reducers';
import { MessageService } from '../../../services/message.service';
import { FinanceiroService } from '../../../services/financeiro.service';
import { currentUser } from '../../../core/auth';
import { ContaReceberFormComponent } from './conta-receber-form/conta-receber-form.component';
import { ContaReceberPaymentComponent } from './conta-receber-payment/conta-receber-payment.component';

@Component({
	selector: 'kt-contas-receber',
	templateUrl: './contas-receber.component.html',
	styleUrls: ['./contas-receber.component.scss']
})
export class ContasReceberComponent implements OnInit {

	filters: any = { status: 1, tipo: 1 };
	permissions: any = {};

	contas = [];

	dataSource = [];
	loading: boolean = false;

	constructor(
		private ref: ChangeDetectorRef,
		private message: MessageService,
		private service: FinanceiroService,
		private modalCtrl: NgbModal,
		private store: Store<AppState>,
	) { }

	ngOnInit() {
		this.load_list();
		this.getPermissions();
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
		this._loading(true);
		this.service.getListContasReceber(this.filters).subscribe(resp => {
			this.dataSource = resp;
			this._loading();
		}, erro => {
			this._loading();
		});
	}

	open(item = {}) {
		const modalRef = this.modalCtrl.open(ContaReceberFormComponent, { size: 'md', backdrop: 'static' });
		modalRef.componentInstance.data = item;
		modalRef.result.then(res => {
			if (res) {
				this.load_list();
			}
		});
	}

	delete(item) {

	}

	pay(item) {
		const contas = [];

		contas.push(item);

		const modalRef = this.modalCtrl.open(ContaReceberPaymentComponent, { size: 'xl', backdrop: 'static' });
		modalRef.componentInstance.data = contas;
		modalRef.result.then(res => {
			if (res) {
				this.contas = [];
				this.load_list();
			}
		});
	}

	paymulti() {
		const modalRef = this.modalCtrl.open(ContaReceberPaymentComponent, { size: 'xl', backdrop: 'static' });
		modalRef.componentInstance.data = this.contas;
		modalRef.result.then(res => {
			if (res) {
				this.contas = [];
				this.load_list();
			}
		});
	}

	checkItem(item) {
		const ind = this.contas.findIndex(e => e.id == item.id);

		if (ind >= 0) { //tem conta remover
			this.contas.splice(ind, 1);
		} else {
			this.contas.push(item);
		}
		console.log(this.contas);
	}

	verificaItem(item) {
		let mark = false;
		this.contas.forEach(e => {
			if (e.id == item.id) {
				mark = true;
			}
		});

		return mark;
	}

	open_filters(content) {
		this.modalCtrl.open(content, { size: 'sm', backdrop: 'static' });
	}

	_loading(type = false) {
		this.loading = type;
		this.ref.detectChanges();
	}

}

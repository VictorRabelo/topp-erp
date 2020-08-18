import { Component, OnInit, ChangeDetectorRef, Input } from '@angular/core';
import { NgbActiveModal, NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from '../../../../services/message.service';
import { FinanceiroService } from '../../../../services/financeiro.service';
import { ClienteSearchComponent } from '../../modais/cliente-search/cliente-search.component';

@Component({
	selector: 'kt-conta-receber-form',
	templateUrl: './conta-receber-form.component.html',
	styleUrls: ['./conta-receber-form.component.scss']
})
export class ContaReceberFormComponent implements OnInit {

	dados: any = {};
	loading: boolean = false;

	@Input() data;

	constructor(
		private ref: ChangeDetectorRef,
		private message: MessageService,
		private service: FinanceiroService,
		private activeModal: NgbActiveModal,
		private modalCtrl: NgbModal
	) { }

	ngOnInit() {
		if (this.data.id > 0) {
			this.dados = this.data;
		}
	}

	close(params = undefined) {
		this.activeModal.close(params);
	}

	_loading(type = false) {
		this.loading = type;
		this.ref.detectChanges();
	}

	getDados() {
		this._loading(true);
		this.service.getContasReceber(this.dados.id).subscribe((resp: any) => {
			this._loading();
			this.dados = resp;
		}, erro => {
			this._loading();
			this.close();
		});
	}

	search_cliente() {
		const modalRef = this.modalCtrl.open(ClienteSearchComponent, { size: 'lg', backdrop: 'static' });
		modalRef.componentInstance.data = { 'nfe': {} };
		modalRef.result.then(res => {
			if (res) {
				this.dados.cliente_id = res.id;
				this.dados.cliente = res.razao;
			}
		});
	}

	submit(form) {
		if (!form.valid) {
			console.log(form);
			return;
		}

		if (this.dados.id > 0) {
			this.update();
		} else {
			this.create();
		}
	}

	create() {
		this._loading(true);
		this.service.createContaReceber(this.dados).subscribe((resp: any) => {
			this._loading();
			this.close(true);
		}, erro => {
			this._loading();
		});
	}

	update() {
		this._loading(true);
		this.service.updateContaReceber(this.dados, this.dados.id).subscribe((resp: any) => {
			this._loading();
			this.close(true);
		}, erro => {
			this._loading();
		});
	}

}

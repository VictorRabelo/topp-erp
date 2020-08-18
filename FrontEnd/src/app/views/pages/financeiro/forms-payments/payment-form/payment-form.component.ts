import { Component, OnInit, Input, ChangeDetectorRef } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { FinanceiroService } from '../../../../../services/financeiro.service';
import { MessageService } from '../../../../../services/message.service';

@Component({
	selector: 'kt-payment-form',
	templateUrl: './payment-form.component.html',
	styleUrls: ['./payment-form.component.scss']
})
export class PaymentFormComponent implements OnInit {

	dados: any = { parcelamento: 0, max_parcelas: 1, more: 0, status: 1 };

	submited = false;
	loadingCep = false;

	@Input() public data;

	constructor(
		private ref: ChangeDetectorRef,
		private message: MessageService,
		private service: FinanceiroService,
		private activeModal: NgbActiveModal,
	) { }

	ngOnInit() {
		console.log(this.data);
		if (this.data != undefined) {
			this.dados = this.data;
			this.getDados();
		}

	}

	close(params = undefined) {
		this.activeModal.close(params);
	}

	getTitle() {
		if (this.dados.id > 0) {
			return "Alterar Cadastro";
		} else {
			return "Nova Cadastro";
		}

	}

	getDados() {
		this.submited = true;
		this.ref.detectChanges();
		this.service.getByIdPayment(this.dados.id).subscribe(resp => {
			this.dados = resp;
			this.submited = false;
			this.ref.detectChanges();
		}, erro => {
			this.close();
		})
	}

	submit(form) {

		if (!form.valid) {
			return;
		}

		// console.log(this.dados);

		if (this.dados.id > 0) {
			this.update();
		} else {
			this.create();
		}

	}

	create() {
		this.submited = true;
		this.ref.detectChanges();
		this.service.createPayment(this.dados).subscribe(resp => {
			this.submited = false;
			// console.log(resp);
			this.close(true);
		}, (erro) => {
			this.submited = false;
			this.ref.detectChanges();
		});
	}

	update() {
		this.submited = true;
		this.ref.detectChanges();
		this.service.updatePayment(this.dados, this.dados.id).subscribe(resp => {
			this.submited = false;
			// console.log(resp);
			this.close(true);
		}, (erro) => {
			this.submited = false;
			this.ref.detectChanges();
		});
	}

}

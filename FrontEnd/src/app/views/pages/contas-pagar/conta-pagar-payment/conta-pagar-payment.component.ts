import { Component, OnInit, ChangeDetectorRef, Input } from '@angular/core';
import { NgbActiveModal, NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from '../../../../services/message.service';
import { FinanceiroService } from '../../../../services/financeiro.service';
import { ToolsService } from '../../../../services/tools.service';
import { PaymentService } from '../../../../services/payment.service';

@Component({
	selector: 'kt-conta-pagar-payment',
	templateUrl: './conta-pagar-payment.component.html',
	styleUrls: ['./conta-pagar-payment.component.scss']
})
export class ContaPagarPaymentComponent implements OnInit {

	contas = [];
	dados: any = { descricao: 'Pagamento de conta' };

	payments = "";
	paymentSource = [];
	paymentsCurrent = [];

	loading: boolean = false;

	@Input() data;

	constructor(
		private ref: ChangeDetectorRef,
		private message: MessageService,
		private service: FinanceiroService,
		private activeModal: NgbActiveModal,
		private modalCtrl: NgbModal,
		private tools: ToolsService,
		private servicePayment: PaymentService
	) {
	}

	ngOnInit() {
		this.dados.data_pago = this.tools.nova_data();
		this.getPayments();

		if (this.data != undefined) {
			this.contas = this.data;

			this.calc_parcelas();
			this.payments_calc_resto();

			console.log(this.data);
		} else {
			this.close();
		}
	}

	close(params = undefined) {
		this.activeModal.close(params);
	}

	_loading(type = false) {
		this.loading = type;
		this.ref.detectChanges();
	}

	submit(form) {
		if (!form.valid) {
			console.log(form);
			return;
		}

		const total_pagar: number = this.dados.total;
		const total_pago: number = this.sum_payments();

		if (this.paymentsCurrent.length == 0) {
			this.message.alertErro('Você não informou a forma de pagamento!');
			return;
		}

		if (total_pago < total_pagar) {
			this.message.alertErro('O valor pago não é suficiente!');
			return;
		}


		this.pagar();
	}

	pagar() {

		const request = {
			'dados': this.dados,
			'payments': this.paymentsCurrent,
			'contas': this.contas
		}

		this._loading(true);
		this.service.paymentContaPagar(request).subscribe((resp: any) => {
			this._loading();
			this.close(true);
		}, erro => {
			this._loading();
		});
	}



	getPayments() {
		this._loading(true);
		this.servicePayment.getList({}).subscribe(resp => {
			this.paymentSource = resp;
			this._loading();
		}, erro => {
			this.close();
		});
	}

	change_payment(ev) {
		const id_pay = ev.target.value;

		const indice = this.paymentSource.findIndex(pay => pay.id == id_pay);

		const paymentSeleted = this.paymentSource[indice];

		const payment = {
			'id': paymentSeleted.id,
			'forma': paymentSeleted.forma,
			'max_parcelas': paymentSeleted.max_parcelas,
			'obs_title': paymentSeleted.obs,
			'valor': 0,
			'resto': this.dados.resto
		}

		this.message.swal.fire({
			title: payment.forma,
			html: `Falta pagar: R$ ${this.tools.money(this.dados.resto)}`,
			input: 'number',
			inputValue: payment.resto,
			inputAttributes: {
				autocapitalize: 'off',
				'placeholder': 'Informe o valor pago...',
				'step': 'any'
			},
			showCancelButton: true,
			cancelButtonText: 'Voltar',
			confirmButtonText: 'Confirmar',
			customClass: { popup: 'swal2-sm' }
		}).then(result => {
			this.payments = '';

			if (result.value && result.value != '') {
				const valor = parseFloat(result.value);

				//verifica se pode inserir mais do que o valor a ser pago
				if (paymentSeleted.more == 0 && valor > this.dados.resto) {
					this.message.alertErro('Pagamento acima do total a pagar', 'Ops!');
					return false;
				}

				//atribui o valor informado
				payment.valor = valor;

				//verifica as opções do pagamento
				if (payment.obs_title != null && payment.obs_title != "") {
					this.changeObs(payment, paymentSeleted)
				} else if (paymentSeleted.parcelamento == 1) {
					this.openModalCrediario(payment, paymentSeleted);
				} else {
					this.set_payment(payment)
				}
			}

		});

	}

	changeObs(payment, paymentSelect) {
		this.message.swal.fire({
			title: `${payment.obs_title}`,
			input: 'text',
			inputValue: payment.obs,
			inputAttributes: {
				autocapitalize: 'off',
				'placeholder': `informe: ${payment.obs_title}...`,
			},
			showCancelButton: true,
			cancelButtonText: 'Voltar',
			confirmButtonText: 'Confirmar',
			customClass: { popup: 'swal2-sm' }
		}).then((result) => {
			console.log(result);
			if (!result.dismiss) {
				payment.obs = result.value;

				if (paymentSelect.parcelamento == 1) {
					this.openModalCrediario(payment, paymentSelect);
				} else {
					this.set_payment(payment);
				}
			}
		});
	}

	openModalCrediario(payment, paymentSelect) {
		this.message.alertErro('Não é possível pagar dívida usando parcelamento!');
		return;
		// const modalRef = this.modalCtrl.open(GeraParcelasComponent, { size: 'lg', backdrop: 'static' });
		// modalRef.componentInstance.data = payment;
		// modalRef.result.then(resp => {
		// 	if (resp != undefined) {
		// 		this.set_payment(resp);
		// 	}
		// });
	}
	set_payment(payment) {
		this.paymentsCurrent.push(payment);
		console.log(this.paymentsCurrent);
		this.payments_calc_resto();
	}
	remove_payment(i) {
		this.paymentsCurrent.splice(i, 1);
		this.payments_calc_resto();
	}

	payments_calc_resto() {
		let total = 0;
		for (let i = 0; i < this.paymentsCurrent.length; i++) {
			total += (isNaN(this.paymentsCurrent[i].valor)) ? 0 : this.paymentsCurrent[i].valor;
		}

		let resto: any = this.dados.total - total;
		this.dados.resto = resto.toFixed(2);
	}

	sum_payments() {
		let pago = 0;

		this.paymentsCurrent.forEach((pay: any) => {
			pago += pay.valor;
		});

		return pago;
	}

	calc_parcelas() {
		let total_all = 0;

		this.contas.forEach(item => {
			console.log(item);

			const valor = item.valor = item.valor;
			const juros = item.juros = (item.juros) ? item.juros : 0;
			const desconto = item.desconto = (item.desconto) ? item.desconto : 0;
			const acrescimo = item.acrescimo = (item.acrescimo) ? item.acrescimo : 0;

			const total = parseFloat(valor) + parseFloat(juros) - parseFloat(desconto) + parseFloat(acrescimo);
			item.valor_pago = parseFloat(total.toFixed(2));

			total_all += total;
		});

		this.dados.total = total_all;

		this.payments_calc_resto();
	}

	trackByIdx(index: number, obj: any): any {
		return index;
	}

}

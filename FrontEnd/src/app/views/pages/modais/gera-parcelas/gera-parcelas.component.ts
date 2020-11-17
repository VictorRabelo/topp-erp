import { Component, OnInit, Input, ChangeDetectorRef } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { ToolsService } from '../../../../services/tools.service';
import { MessageService } from '../../../../services/message.service';

@Component({
	selector: 'kt-gera-parcelas',
	templateUrl: './gera-parcelas.component.html',
	styleUrls: ['./gera-parcelas.component.scss']
})
export class GeraParcelasComponent implements OnInit {

	payment: any = { status_pago: 0 };
	screen: number;

	loading: boolean = false;

	@Input() data;

	constructor(
		private ref: ChangeDetectorRef,
		private util: ToolsService,
		private message: MessageService,
		private modalActive: NgbActiveModal,
	) {
		this.screen = this.util.getScreen();
	}

	ngOnInit() {
		this.payment = this.data;
		console.log(this.payment);
		this.gera_parcelas();
	}

	close(params = undefined) {
		this.modalActive.close(params);
	}

	gera_parcelas() {

		const valor = this.payment.valor;
		const entrada = (isNaN(this.payment.entrada)) ? 0 : this.payment.entrada;
		const qtd_parcelas = this.payment.qtd_parcelas;
		const max_parcelas = this.payment.max_parcelas;
		const aparcelar = valor - entrada;
		let data_start = this.payment.data_start;

		const valor_parcela = (valor - entrada) / qtd_parcelas;

		this.payment.parcelas = [];

		if (entrada > valor) {//verifica o valor da entrada e o valor a ser parcelado!
			this.message.alertErro('Valor da Entrada não pode ser maior que o Valor Parcelado!', 'Ops!');
			this.payment.entrada = 0;
			return false;
		}

		this.payment.aparcelar = aparcelar;

		// console.log(this.payment.data_start);
		if (!data_start || data_start == '') { //verifica se a data é igual a de hoje, caso for adiciona um mes
			data_start = this.util.nova_data(1);
		} else {
			data_start = this.payment.data_start;
		}

		if (max_parcelas < qtd_parcelas) {
			this.message.alertErro(`A quantidade de parcelas não pode ultrapassar: ${max_parcelas}`, 'Ops!');
			this.payment.qtd_parcelas = 0;
			return false;
		}

		let ano: any = parseFloat(data_start.split('-')[0]);
		let mes: any = parseFloat(data_start.split('-')[1]);
		let diap: any = parseFloat(data_start.split('-')[2]);

		for (let i = 0; i < qtd_parcelas; i++) {
			let num = i + 1;

			let dia = diap;

			if (mes >= 13) { //se passar do mês 12, então inicia com o mes 1 do próximo ano
				mes = 1;
				ano = ano + 1;
			}

			if (mes == 4 && dia > 30 || mes == 6 && dia > 30 || mes == 9 && dia > 30 || mes == 11 && dia > 30) {
				dia = 30;
			} else if (mes == 2 && dia > 28) {
				dia = 28;
			}

			if (dia < '10') {
				dia = '0' + dia;
			}
			if (mes < '10') {
				mes = '0' + mes;
			}

			let data_vence = ano + '-' + mes + '-' + dia;

			this.payment.parcelas.push({ 'num': num, 'data_vence': data_vence, 'valor': valor_parcela });

			mes = parseFloat(mes) + 1;

		}
		console.log(this.payment);

	}
	confirm_parcelas() {

		if (this.payment.parcelas.length <= 0) {
			this.message.alertErro('Você não gerou nenhuma parcela!', 'Ops!');
			return false;
		}

		this.close(this.payment);

	}

}

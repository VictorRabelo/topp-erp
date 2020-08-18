import { Component, OnInit, Input, ChangeDetectorRef } from '@angular/core';
import { FiscalService } from '../../../../services/fiscal.service';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from '../../../../services/message.service';

@Component({
	selector: 'kt-fiscal-send-xml',
	templateUrl: './fiscal-send-xml.component.html',
	styleUrls: ['./fiscal-send-xml.component.scss']
})
export class FiscalSendXmlComponent implements OnInit {

	dados: any = { modelo: 55, tpamb: 1 };

	dataSource: any = [];

	loading: boolean = false;

	@Input() emitente;

	constructor(
		private ref: ChangeDetectorRef,
		private message: MessageService,
		private serviceFiscal: FiscalService,
		private activeModal: NgbActiveModal
	) { }

	ngOnInit() {
		console.log(this.emitente)
		this.dados.empresa_id = this.emitente.empresa_id;
		this.dados.emitente_id = this.emitente.id;
		this.dados.emitente = this.emitente.razao;
		this.dados.cnpj = this.emitente.cnpj;
		this.dados.emailSend = this.emitente.email_contador;
		this.getMeses();
	}

	close(params = undefined) {
		this.activeModal.close(params);
	}
	getMeses() {
		this.loading = true;
		this.serviceFiscal.getMeses(this.dados).subscribe(resp => {
			this.loading = false;
			this.ref.detectChanges();

			this.dataSource = resp;

		}, () => {
			this.loading = false;
			this.ref.detectChanges();
		})
	}

	send() {
		this.loading = true;
		this.serviceFiscal.sendXML(this.dados).subscribe(resp => {

			this.close(true);
			this.loading = false;
			this.ref.detectChanges();

		}, (erro) => {
			this.message.alertErro(erro.error.text);
			this.loading = false;
			this.ref.detectChanges();
		})
	}

}

import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from '../../../../services/message.service';
import { NFeService } from '../../../../services/nfe.service';
import { ToolsService } from '../../../../services/tools.service';
import { PaymentService } from '../../../../services/payment.service';
import { ClienteSearchComponent } from '../../modais/cliente-search/cliente-search.component';
import { ProdutoSearchComponent } from '../../modais/produto-search/produto-search.component';
import { ProdutoDetalheComponent } from '../../modais/produto-detalhe/produto-detalhe.component';
import { GeraNotaComponent } from '../../modais/gera-nota/gera-nota.component';

import { Store, select } from '@ngrx/store';
import { AppState } from '../../../../core/reducers';
import { currentUser } from '../../../../core/auth';

@Component({
	selector: 'kt-nfe-form',
	templateUrl: './nfe-form.component.html',
	styleUrls: ['./nfe-form.component.scss']
})
export class NfeFormComponent implements OnInit {

	loading: boolean = false;

	nfe: any = {};
	ItemSource = [];
	paymentSource = [];
	sourceReferences = [];

	paymentSelect = [];
	selectedReferences = [];

	payment: string = "";
	keyword = "";

	permissions: any = {};

	constructor(
		private ref: ChangeDetectorRef,
		private message: MessageService,
		private service: NFeService,
		private servicePayment: PaymentService,
		private util: ToolsService,
		private ActiveRoute: ActivatedRoute,
		private modalCtrl: NgbModal,
		private router: Router,
		private store: Store<AppState>,
	) {
		this.getPayments();
	}

	ngOnInit() {
		this.loading = true;
		this.ActiveRoute.params.subscribe(params => {
			if (params['id']) {
				const id = params['id'];
				this.getDados(id);
				this.getItens(id);
			}
			else {
				this.close();
			}
		});

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

	close() {
		this.router.navigate(['/nfe']);
	}
	blockInput() {
		let block = false;
		if (this.nfe.cstatus == 100 || this.nfe.cstatus == 101 || this.nfe.cstatus == 135 || this.nfe.cstatus == 155) {
			block = true;
		}

		return block;
	}

	getPayments() {
		this.loading = true;
		this.servicePayment.getList({}).subscribe(resp => {
			this.paymentSource = resp;
			this._loading();
		}, (erro) => {
			this.close();
		});
	}

	getDados(id) {
		this.loading = true;
		this.service.getById(id).subscribe((resp: any) => {
			this.loading = false;

			this.nfe = resp;
			if (resp.finalidade_nf != 1) {
				this.getReferences();
			}
			// this.getPayments();

			this.ref.detectChanges();
		}, erro => {
			this._loading();
		});
	}

	getItens(id_nfe) {
		this.loading = true;
		this.service.getListItens({ nfe_id: id_nfe }).subscribe((resp: any) => {
			this.ItemSource = resp.itens;
			this.nfe.subtotal = resp.totais.subtotal;
			this.nfe.total = resp.totais.total;
			this.paymentSelect = resp.payments;
			this.payments_calc_resto();
			this._loading();
		}, (erro) => {
			this._loading();
		});
	}


	//Cliente
	add_cliente() {
		if (this.nfe.cstatus == 100 || this.nfe.cstatus == 101 || this.nfe.cstatus == 135 || this.nfe.cstatus == 155) {
			return false;
		}

		const modalRef = this.modalCtrl.open(ClienteSearchComponent, { size: 'lg', backdrop: 'static' });
		modalRef.componentInstance.data = { nfe: this.nfe };
		modalRef.result.then(resp => {
			if (resp) {
				this.nfe.cliente_id = resp.id;
				this.nfe.razao = resp.razao;
				this.nfe.fantasia = resp.fantasia;
				this.nfe.cnpj = resp.cnpj;
				this.nfe.inscricao_estadual = resp.inscricao_estadual;
				this.nfe.logradouro = resp.logradouro;
				this.nfe.numero = resp.numero;
				this.nfe.bairro = resp.bairro;
				this.nfe.complemento = resp.complemento;
				this.nfe.cidade = resp.cidade;
				this.nfe.uf = resp.uf;
				this.nfe.ibge = resp.ibge;
				this.ref.detectChanges();
			}
		});
	}

	//Produtos
	search_item() {
		const modalRef = this.modalCtrl.open(ProdutoSearchComponent, { size: 'xl', backdrop: 'static' });
		modalRef.componentInstance.data = { 'nfe': this.nfe };
		modalRef.result.then(resp => {
			if (resp) {
				console.log(resp)
				this.open_item(resp);
			}
		})
	}

	open_item(item) {
		const modalRef = this.modalCtrl.open(ProdutoDetalheComponent, { size: 'lg', backdrop: 'static' });
		modalRef.componentInstance.data = { 'item': item, 'nfe': this.nfe };
		modalRef.result.then(resp => {
			if (resp) {
				resp.nfe_id = this.nfe.id;

				if (resp.id) {
					this.update_item(resp);
				} else {
					this.add_item(resp);
				}
			}
		});
	}

	add_item(item) {
		this.loading = true;
		this.service.create_item(item).subscribe(resp => {
			this._loading();
			this.getItens(this.nfe.id);
		}, erro => {
			this._loading();
		});
	}
	update_item(item) {
		this.loading = true;
		this.service.update_item(item, item.id).subscribe(resp => {
			this._loading();
			this.getItens(this.nfe.id);
		}, erro => {
			this._loading();
		});
	}

	del_item_confirm(item) {
		this.message.swal.fire({
			// title: 'Remover ?',
			icon: 'question',
			html: `Desaja remover o item: <br><b>${item.descricao}</b> ?`,
			confirmButtonText: 'Confirmar',
			showCancelButton: true,
			cancelButtonText: 'Não',
		}).then((result) => {
			if (!result.dismiss) {
				this.delete_item(item);
			}
		});
	}

	delete_item(item) {
		if (this.nfe.cstatus == 100 || this.nfe.cstatus == 101 || this.nfe.cstatus == 135 || this.nfe.cstatus == 155) {
			return false;
		}

		this._loading(true);
		this.service.delete_item(item.id).subscribe((resp: any) => {
			this.getItens(this.nfe.id);
		}, erro => {
			this._loading();
		});
	}

	//referencias
	getReferences() {
		this.service.get_references(this.nfe.id).subscribe((resp: any) => {
			this.selectedReferences = resp;
			this.ref.detectChanges();
		});
	}
	selectEvent(item) {
		item.nfe_id = this.nfe.id;

		this._loading(true);
		this.service.create_references(item).subscribe((resp: any) => {
			this.keyword = "";
			this.sourceReferences = [];
			this._loading();

			this.getReferences();
		});
	}
	onChangeSearch(ev) {
		const termo = this.keyword;

		this.service.get_search_references({ 'termo': termo }).subscribe((resp: []) => {
			this.sourceReferences = resp;
			this.ref.detectChanges();
		});
	}
	onChangeDel(item) {
		this._loading(true);
		this.service.delete_references(item.id).subscribe((resp: any) => {
			this._loading();
			this.getReferences();
		});
	}
	//end referencias


	//pagamentos
	change_payment(ev) {
		const id_pay = ev.target.value;

		const indice = this.paymentSource.findIndex(pay => pay.id == id_pay);

		const paymentSeleted = this.paymentSource[indice];

		const payment = {
			'id': paymentSeleted.id,
			'forma': paymentSeleted.forma,
			'max_parcelas': paymentSeleted.max_parcelas,
			'obs_title': paymentSeleted.obs,
			'valor': 0
		}

		this.message.swal.fire({
			title: payment.forma,
			html: `Falta pagar: R$ ${this.util.money(this.nfe.resto)}`,
			input: 'number',
			inputValue: this.nfe.resto,
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
			this.payment = '';

			if (result.value && result.value != '') {
				const valor = parseFloat(result.value);

				//verifica se pode inserir mais do que o valor a ser pago
				if (paymentSeleted.more == 0 && valor > this.nfe.resto) {
					this.message.alertErro('Pagamento acima do total a pagar', 'Ops!');
					return false;
				}

				//atribui o valor informado
				payment.valor = valor;

				//verifica as opções do pagamento
				if (payment.obs_title != null && payment.obs_title != "") {
					this.changeObs(payment, paymentSeleted)
				} else if (paymentSeleted.parcelamento == 1) {
					// this.openModalCrediario(payment, paymentSeleted);
					this.set_payment(payment);
				} else {
					this.set_payment(payment)
				}
			} else {
				this.payment = "";
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
			if (!result.dismiss) {
				payment.obs = result.value;

				if (paymentSelect.parcelamento == 1) {
					// this.openModalCrediario(payment, paymentSelect);
					this.set_payment(payment);
				} else {
					this.set_payment(payment);
				}
			} else {
				this.payment = "";
			}
		});
	}

	// openModalCrediario(payment, paymentSelect) {
	//    const modalRef = this.modalCtrl.open(GeraParcelasComponent, { size: 'lg', backdrop: 'static' });
	//    modalRef.componentInstance.data = payment;
	//    modalRef.result.then(resp => {
	//       if (resp != undefined) {
	//          this.set_payment(resp);
	//       }
	//    });
	// }
	set_payment(payment) {
		payment.nfe_id = this.nfe.id;
		payment.forma_id = payment.id;

		this._loading(true)
		this.service.create_payment(payment).subscribe((resp: any) => {
			this.loading = false;
			this.payment = "";
			this.ref.detectChanges();
			this.getItens(this.nfe.id);
		}, erro => {
			this._loading();
		});
		// this.paymentSelect.push(payment);
		// console.log(this.paymentSelect);
		// this.payments_calc_resto();
	}
	remove_payment(item) {
		this._loading(true)
		this.service.delete_payment(item.id).subscribe((resp: any) => {
			this._loading();
			this.getItens(this.nfe.id);
		}, erro => {
			this._loading();
		});
		// this.paymentSelect.splice(i, 1);
		// this.payments_calc_resto();
	}

	payments_calc_resto() {
		let total = 0;
		for (let i = 0; i < this.paymentSelect.length; i++) {
			total += (isNaN(this.paymentSelect[i].valor)) ? 0 : this.paymentSelect[i].valor;
		}

		let resto: any = this.nfe.total - total;
		this.nfe.resto = resto.toFixed(2);
	}

	sum_payments() {
		let pago = 0;

		this.paymentSelect.forEach(pay => {
			pago += pay.valor;
		});

		return pago;
	}

	verificaParcelas() {
		let tem = false;
		this.paymentSelect.forEach(pay => {
			if (pay.parcelas) {
				tem = true;
			}
		});

		return tem;
	}

	del_payment_confirm() {

	}

	save() {
		this.loading = true;
		this.service.update(this.nfe, this.nfe.id).subscribe((resp: any) => {
			this.ItemSource = resp.itens;
			this._loading();
			this.getDados(this.nfe.id);
			this.getItens(this.nfe.id);
		}, (erro) => {
			this._loading();
		});
	}

	selectEmitente() {
		const modalRef = this.modalCtrl.open(GeraNotaComponent, { size: 'md', backdrop: 'static' });
		modalRef.componentInstance.data = { modelo: 55 };
		modalRef.result.then(resp => {
			if (resp) {
				console.log(resp);
				this.nfe.emitente_id = resp.emitente_id;
				this.sendNFe();
				// this.getDados(this.nfe.id);
				// this.getItens(this.nfe.id);
			}
		});
	}

	sendNFe() {
		this.loading = true;
		this.ref.detectChanges();
		this.service.emitir(this.nfe).subscribe((resp: any) => {
			if (resp.pdf_url) {
				window.open(resp.pdf_url, '_blank');
			}
			this.getDados(this.nfe.id);
			this._loading();
		}, erro => {
			const message = (erro.error.text) ? erro.error.text : erro.error.message
			this.message.alertErro(message);
			this._loading();
		});
	}

	add_just() {
		this.message.swal.fire({
			title: `Cancelar esta NFe ?`,
			text: 'Informe uma justificativa para o cancelamento...',
			input: 'textarea',
			inputPlaceholder: 'Ex: Falha ao digitar dados...',
			showCancelButton: true,
			confirmButtonText: 'Confirmar',
			cancelButtonText: 'Voltar'
		}).then(resp => {
			if (resp.value) {
				this.nfe.xjust = resp.value;
				this.cancela_nfe();
			}
		});
	}

	cancela_nfe() {
		this.loading = true;
		this.ref.detectChanges();
		this.service.cancelar(this.nfe).subscribe((resp: any) => {
			if (resp.pdf_url) {
				window.open(resp.pdf_url, '_blank');
			}
			this.getDados(this.nfe.id);
			this._loading();
		}, erro => {
			this.message.alertErro(erro.error.text);
			this._loading();
		});
	}

	printNFe() {
		this.loading = true;
		this.ref.detectChanges();
		this.service.print(this.nfe.id).subscribe((resp: any) => {
			if (resp.pdf_url) {
				window.open(resp.pdf_url, '_blank');
			}
			this._loading();
		}, erro => {
			this.message.alertErro(erro.error.text);
			this._loading();
		});
	}

	_loading(type = false) {
		this.loading = type;
		this.ref.detectChanges();
	}

}

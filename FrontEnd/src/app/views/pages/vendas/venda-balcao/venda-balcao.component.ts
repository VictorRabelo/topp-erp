import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Store, select } from '@ngrx/store';
import { MessageService } from '../../../../services/message.service';
import { VendaService } from '../../../../services/venda.service';
import { ToolsService } from '../../../../services/tools.service';
import { AppState } from '../../../../core/reducers';
import { currentUser } from '../../../../core/auth';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ProdutoSearchComponent } from '../../modais/produto-search/produto-search.component';
import { ProdutoDetalheComponent } from '../../modais/produto-detalhe/produto-detalhe.component';
import { ClienteSearchComponent } from '../../modais/cliente-search/cliente-search.component';
import { VendaFinishComponent } from '../../modais/venda-finish/venda-finish.component';
import { GeraNotaComponent } from '../../modais/gera-nota/gera-nota.component';
import { FiscalService } from '../../../../services/fiscal.service';
import { NFeService } from '../../../../services/nfe.service';
import { ProductService } from '../../../../services/product.service';

@Component({
	selector: 'kt-venda-balcao',
	templateUrl: './venda-balcao.component.html',
	styleUrls: ['./venda-balcao.component.scss']
})
export class VendaBalcaoComponent implements OnInit {

	loading: boolean = false;
	loadingItens: boolean = false;

	permissions: any = {};

	vendaCurrent: any = {}
	cliente: any = {};
	vendedor: any = {};
	ItemSource = [];
	paymentSource = [];

	input_search: string = "";

	constructor(
		private ref: ChangeDetectorRef,
		private message: MessageService,
		private service: VendaService,
		private serviceNFe: NFeService,
		private serviceFiscal: FiscalService,
		private serviceProduto: ProductService,
		private util: ToolsService,
		private ActiveRoute: ActivatedRoute,
		private modalCtrl: NgbModal,
		private store: Store<AppState>,
		private router: Router
	) {
		this.getPermissions();
	}

	ngOnInit() {
		this._loading(true);
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
	}

	close() {
		this.router.navigate(['/vendas']);
	}

	_loading(type = false) {
		this.loading = type;
		this.ref.detectChanges();
	}

	getPermissions() {
		this.store.pipe(select(currentUser)).subscribe((resp: any) => {
			if (resp) {
				// console.log(resp.permissions);
				this.permissions = resp.permissions;
			}
		});
	}

	getTitulo() {
		if (this.vendaCurrent.id > 0) {
			return "Venda: " + this.vendaCurrent.id;
		} else {
			return "Venda Fechada";
		}
	}

	getDados(id) {
		this._loading(true);
		this.service.getById(id).subscribe((resp: any) => {
			this.vendaCurrent = resp;
			this._loading();
		}, erro => {
			this.close();
			this.message.toastError('Venda não localizada!');
			this._loading();
		});
	}

	getItens(id_venda) {
		this.loadingItens = true;
		this.service.getListItens(id_venda).subscribe((resp: any) => {
			this.ItemSource = resp.itens;
			this.vendaCurrent.descontos = resp.totais.descontos + parseFloat(this.vendaCurrent.desconto);
			this.vendaCurrent.subtotal = resp.totais.subtotal
			this.vendaCurrent.total = resp.totais.subtotal - this.vendaCurrent.descontos;

			this.paymentSource = resp.payments;

			this.loadingItens = false;
			this.ref.detectChanges();
		}, erro => {
			this.loadingItens = false;
			this.ref.detectChanges();
		});
	}
	getTroco() {
		let troco = 0;
		this.paymentSource.forEach(payment => {
			troco += parseFloat(payment.valor);
		});

		troco = troco - parseFloat(this.vendaCurrent.total);

		return troco.toFixed(2);
	}

	search_produto(ev) {
		// console.log(ev);
		const code: string = ev.target.value;
		if (code.length <= 0) {
			return false;
		}

		this._loading(true);
		this.ref.detectChanges();
		this.serviceProduto.getList({ termo: code }).subscribe(resp => {
			this._loading();
			this.ref.detectChanges();

			if (resp.length > 1) {
				this.search_item();
			} else if (resp.length == 1) {

				const item = {
					'desconto': 0,
					'descontop': 0,
					'descricao': resp[0].descricao,
					'foto': resp[0].foto,
					'produto_id': resp[0].id,
					'quantidade': 1,
					'total': resp[0].preco,
					'valor_unitario': resp[0].preco
				}
				this.input_search = "";
				this.open_item(item);
			} else {
				this.message.alertErro('Produto não encontrado!');
				this.search_item();
			}

		}, erro => {
			this._loading();
			this.ref.detectChanges();
		});

	}

	search_item() {
		const modalRef = this.modalCtrl.open(ProdutoSearchComponent, { size: 'xl', backdrop: 'static' });
		modalRef.componentInstance.data = { 'venda': this.vendaCurrent };
		modalRef.result.then(resp => {
			if (resp) {
				this.open_item(resp);
			}
		})
	}

	open_item(item) {
		const modalRef = this.modalCtrl.open(ProdutoDetalheComponent, { size: 'lg', backdrop: 'static' });
		modalRef.componentInstance.data = { 'item': item, 'venda': this.vendaCurrent };
		modalRef.result.then(resp => {
			if (resp) {
				resp.venda_id = this.vendaCurrent.id;

				if (resp.id) {
					this.update_item(resp);
				} else {
					this.add_item(resp);
				}
			}
		});
	}

	add_item(item) {
		this._loading(true);
		this.ref.detectChanges();
		this.service.create_item(item).subscribe(resp => {
			this._loading();
			this.ref.detectChanges();
			this.getItens(this.vendaCurrent.id);
		}, erro => {
			this._loading();
			this.ref.detectChanges();
		});
	}
	update_item(item) {
		this._loading(true);
		this.ref.detectChanges();
		this.service.update_item(item, item.id).subscribe(resp => {
			this._loading();
			this.ref.detectChanges();
			this.getItens(this.vendaCurrent.id);
		}, erro => {
			this._loading();
			this.ref.detectChanges();
		});
	}

	del_item_confirm(item) {
		this.message.swal.fire({
			// title: 'Remover ?',
			icon: 'question',
			html: `Deseja remover o item: <br><b>${item.descricao}</b> ?`,
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
		if (this.vendaCurrent.status != 1) {
			return false;
		}

		this.loadingItens = true;
		this.ref.detectChanges();
		this.service.delete_item(item.id).subscribe((resp: any) => {
			this.getItens(this.vendaCurrent.id);
		}, erro => {
			this.loadingItens = false;
			this.ref.detectChanges();
		});
	}

	add_cliente() {
		if (this.vendaCurrent.status != 1) {
			return false;
		}

		const modalRef = this.modalCtrl.open(ClienteSearchComponent, { size: 'lg', backdrop: 'static' });
		modalRef.componentInstance.data = this.vendaCurrent;
		modalRef.result.then(resp => {
			if (resp) {
				this.setClient(resp);
			}
		});
	}

	setClient(item) {
		const request = {
			'cliente_id': item.id,
			'cliente': item.razao,
			'cpf': item.cnpj,
		}

		this._loading(true);
		this.service.setClient(request, this.vendaCurrent.id).subscribe(resp => {
			this.getDados(this.vendaCurrent.id);
			this.getItens(this.vendaCurrent.id);
		}, erro => {
			this._loading();
			this.ref.detectChanges();
		})
	}

	finish() {
		if (this.vendaCurrent.status != 1) {
			return false;
		}

		const modalRef = this.modalCtrl.open(VendaFinishComponent, { size: 'md', backdrop: 'static' });
		modalRef.componentInstance.data = this.vendaCurrent;
		modalRef.result.then(resp => {
			if (resp) {
				this.getDados(this.vendaCurrent.id);
				this.getItens(this.vendaCurrent.id);
			}
		});
	}

	cancel_sale() {
		this.message.swal.fire({
			title: 'Cancelar ?',
			icon: 'question',
			html: `Deseja cancelar a venda ?`,
			confirmButtonText: 'Confirmar',
			showCancelButton: true,
			cancelButtonText: 'Não',
		}).then((result) => {
			if (!result.dismiss) {
				this._loading(true);
				this.service.cancel(this.vendaCurrent.id).subscribe(resp => {
					this.close();
				}, erro => {
					this._loading();
					this.ref.detectChanges();
				})
			}
		});
	}

	gen_nota(modelo) {
		const modalRef = this.modalCtrl.open(GeraNotaComponent, { size: 'md', backdrop: 'static' });
		modalRef.componentInstance.data = { 'venda': this.vendaCurrent, modelo: modelo };
		modalRef.result.then(resp => {
			if (resp) {
				this.getDados(this.vendaCurrent.id);
				this.getItens(this.vendaCurrent.id);
			}
		});
	}

	gen_nfe() {
		this.loadingItens = true;
		this.ref.detectChanges();
		this.service.geraNFe(this.vendaCurrent).subscribe((resp: any) => {
			if (resp.nfe_id) {
				this.router.navigate([`/nfe/${resp.nfe_id}`]);
			}
			this.loadingItens = false;
			this.ref.detectChanges();
		}, erro => {
			this.loadingItens = false;
			this.ref.detectChanges();
		});
	}
	printNFe() {
		this._loading(true);
		this.ref.detectChanges();
		this.serviceNFe.print(this.vendaCurrent.nfe.id).subscribe((resp: any) => {
			if (resp.pdf_url) {
				window.open(resp.pdf_url, '_blank');
			}
			this._loading();
			this.ref.detectChanges();
		}, erro => {
			this.message.alertErro(erro.error.text);
			this._loading();
			this.ref.detectChanges();
		});
	}

	print_nota(modelo) {
		this.loadingItens = true;
		this.ref.detectChanges();
		this.serviceFiscal.printNFCe(this.vendaCurrent.nfce).subscribe((resp: any) => {
			if (resp.pdf_url) {
				window.open(resp.pdf_url, '_blank');
			}
			this.loadingItens = false;
			this.ref.detectChanges();
		}, erro => {
			this.loadingItens = false;
			this.ref.detectChanges();
		});
	}

	print_sale() {
		this.loading = true;
		this.ref.detectChanges();

		this.service.printSale(this.vendaCurrent.id).subscribe(async (resp: any) => {
			await this.util.send_print(resp);
			this.loading = false;
			this.ref.detectChanges();
		}, erro => {
			this.loading = false;
			this.ref.detectChanges();
		});
	}

}

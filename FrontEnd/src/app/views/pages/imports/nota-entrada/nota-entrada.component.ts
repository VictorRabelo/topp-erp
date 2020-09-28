import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { FiscalService } from '../../../../services/fiscal.service';
import { MessageService } from '../../../../services/message.service';

@Component({
	selector: 'kt-nota-entrada',
	templateUrl: './nota-entrada.component.html',
	styleUrls: ['./nota-entrada.component.scss']
})
export class NotaEntradaComponent implements OnInit {

	loading: boolean = false;

	nota: any = { fornecedor: {}, ide: {}, fatura: {}, itens: [] };
	product: any = {};

	filters: any = {};

	constructor(
		private ref: ChangeDetectorRef,
		private redirect: Router,
		private route: ActivatedRoute,
		private modalCtrl: NgbModal,
		private service: FiscalService,
		private message: MessageService,
	) { }

	ngOnInit() {
		this.route.queryParams.subscribe(params => {
			console.log(params);
			if (params.chave && params.emitente) {
				this.filters = params;
				this.getLoadXML();
			} else {
				this.close();
			}
		});
	}

	close() {
		this.redirect.navigateByUrl('/monitor-fiscal');
	}

	_loading(type = false) {
		this.loading = type;
		this.ref.detectChanges();
	}

	getLoadXML() {
		this._loading(true);
		this.service.getDadosXML(this.filters).subscribe(resp => {
			this.nota = resp;
			this._loading();
		}, erro => {
			this.close();
			this._loading();
		});
	}

	calc_preco() {
		const custo = parseFloat(this.product.custo);
		const margem = parseFloat(this.product.margem);
		let preco = 0;

		if (custo > 0 && margem > 0) {
			preco = (margem / 100);
			preco = (custo * preco) + custo;
		}

		this.product.preco = preco;
	}
	calc_margem() {
		const custo = (this.product.custo) ? this.product.custo : 0;
		let margem = 0;
		let preco = (this.product.preco) ? this.product.preco : 0;

		if (custo > 0 && preco > 0) {
			margem = (preco - custo) / custo;
			margem = margem * 100;
		}

		this.product.custo = custo;
		this.product.margem = margem;
		// this.product.preco = preco;

	}

	open_item(content, item, i) {
		this.product = Object.assign({}, item);
		this.modalCtrl.open(content, { size: 'md', backdrop: 'static' });
	}
	edit_item() {

		for (let i = 0; i < this.nota.itens.length; i++) {
			if (this.nota.itens[i].referencia == this.product.referencia) {
				this.nota.itens[i] = this.product;
			}
		}
		this.ref.detectChanges();
	}

	open_change_margem() {
		this.message.swal.fire({
			title: 'Ajuste de Preço',
			html: 'Ajuste o preço informando a margem!',
			icon: 'info',
			input: 'number',
			inputPlaceholder: 'informe o valor da margem',
			inputAttributes: {
				"step": 'any'
			},
			confirmButtonText: 'Confirmar',
			showCancelButton: true,
			cancelButtonText: 'Voltar'
		}).then(resp => {
			if (resp.value) {
				this.change_margem(resp.value);
			}
		})
	}
	change_margem(margem) {
		for (let i = 0; i < this.nota.itens.length; i++) {

			const custo = parseFloat(this.nota.itens[i].custo);
			// const margem = parseFloat(this.nota.itens[i].margem);
			let preco = 0;

			if (custo > 0 && margem > 0) {
				preco = (margem / 100);
				preco = (custo * preco) + custo;
			}

			this.nota.itens[i].margem = margem;
			this.nota.itens[i].preco = preco;

		}
		this.ref.detectChanges();
	}

	submit() {
		this.message.swal.fire({
			title: 'Importar ?',
			html: 'Deseja importar os esses dados ?',
			icon: 'question',
			// input: 'number',
			// inputPlaceholder: 'informe o valor da margem',
			// inputAttributes: {
			// 	"step": 'any'
			// },
			confirmButtonText: 'Confirmar',
			showCancelButton: true,
			cancelButtonText: 'Voltar'
		}).then(resp => {
			if (resp.value) {
				this.importDados();
			}
		})

	}

	importDados() {
		this._loading(true);
		this.service.importDadosXML(this.nota, this.filters.emitente).subscribe(resp => {
			// this.nota = resp;
			this.close();
			this._loading();
		}, erro => {
			this._loading();
		});
	}

}

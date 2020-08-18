import { Component, OnInit, Input, ChangeDetectorRef } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { ProductService } from '../../../../services/product.service';
import { ToolsService } from '../../../../services/tools.service';
import { tap, finalize } from 'rxjs/operators';

@Component({
	selector: 'kt-produto-search',
	templateUrl: './produto-search.component.html',
	styleUrls: ['./produto-search.component.scss']
})
export class ProdutoSearchComponent implements OnInit {

	loading = false;

	dataSource = [];
	screen: number;

	filters: any = {};

	@Input() data;

	constructor(
		private ref: ChangeDetectorRef,
		private service: ProductService,
		private util: ToolsService,
		private modalActive: NgbActiveModal
	) {
		this.screen = this.util.getScreen();

	}

	ngOnInit() {
		// if (this.data.filters) {
		// 	this.filters = this.data.filters;
		// }
		this.listProdutos();
	}

	close(params = undefined) {
		this.modalActive.close(params);
	}

	listProdutos() {
		this.loading = true;
		this.service.getList(this.filters).subscribe(resp => {
			this.dataSource = resp;
			this.loading = false;
			this.ref.detectChanges();
		}, erro => {
			this.loading = false;
			this.ref.detectChanges();
		});
	}

	chage(item) {

		let retorno: any = {
			'produto_id': item.id,
			'descricao': item.descricao,
			'quantidade': 1,
			'valor_unitario': item.preco,
			'desconto': 0,
			'foto': item.foto_url
		}

		if (this.data.nfe) {
			retorno.cfop = item.cfop;

			retorno.cst_icms = item.cst_icms;
			retorno.p_icms = item.p_icms;

			retorno.cst_ipi = item.cst_ipi;
			retorno.p_ipi = item.p_ipi;

			retorno.cst_pis = item.cst_pis;
			retorno.p_pis = item.p_pis;

			retorno.cst_cofins = item.cst_cofins;
			retorno.p_cofins = item.p_cofins;
		}

		this.close(retorno);

	}

}

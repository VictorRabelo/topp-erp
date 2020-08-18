import { Component, OnInit, Input, ChangeDetectorRef } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { ToolsService } from '../../../../services/tools.service';
import { VendaService } from '../../../../services/venda.service';

@Component({
	selector: 'kt-produto-detalhe',
	templateUrl: './produto-detalhe.component.html',
	styleUrls: ['./produto-detalhe.component.scss']
})
export class ProdutoDetalheComponent implements OnInit {

	loading = false;

	vendaCurrent: any = {};
	item: any = {};
	screen: number;

	@Input() data;

	constructor(
		private ref: ChangeDetectorRef,
		private service: VendaService,
		private util: ToolsService,
		private modalActive: NgbActiveModal
	) {
		this.screen = this.util.getScreen();
	}

	ngOnInit() {
		if (this.data.item) {
			this.item = this.data.item;
		}
		if (this.data.venda) {
			this.vendaCurrent = this.data.venda;
		}
		if (this.data.nfe) {
			this.vendaCurrent = this.data.nfe;
		}
		this.calc_desconto();
		console.log(this.item);
	}

	close(params = undefined) {
		this.modalActive.close(params);
	}

	submit() {
		if (this.item.id) {
			this.edit_item();
		} else {
			this.add_item();
		}
	}

	add_item() {

		// this.item.venda_id = this.vendaCurrent.id;

		// this.loading = true;
		// this.service.create_item(this.item).subscribe(resp => {
		this.close(this.item);
		// }, erro => {
		//    this.loading = false;
		//    this.ref.detectChanges();
		// });
	}

	edit_item() {

		// this.loading = true;
		// this.service.update_item(this.item, this.item.id).subscribe(resp => {
		this.close(this.item);
		// }, erro => {
		//    this.loading = false;
		//    this.ref.detectChanges();
		// });
	}

	calc_desconto(type = 1) {
		const valor = this.item.valor_unitario * this.item.quantidade;
		let desc = this.item.desconto;
		let pdesc = (this.item.descontop > 0) ? this.item.descontop : 0;

		if (type == 1) {
			pdesc = (desc / valor) * 100;
			console.log('tipo 1');
		} else {
			desc = (pdesc / 100) * valor;
			console.log('tipo 2');
		}

		this.item.desconto = desc;
		this.item.descontop = pdesc;

		this.item.total = valor - desc;
	}

}

import { Component, OnInit, ChangeDetectorRef, Input } from '@angular/core';
import { NgbActiveModal, NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ToolsService } from '../../../services/tools.service';
import { ProductService } from '../../../services/product.service';

@Component({
	selector: 'kt-variations',
	templateUrl: './variations.component.html',
	styleUrls: ['./variations.component.scss']
})
export class VariationsComponent implements OnInit {

	loading = false;

	variations = [];
	filters: any = {};

	@Input() data;

	constructor(
		private ref: ChangeDetectorRef,
		private modalActive: NgbActiveModal,
		private modalCtrl: NgbModal,
		private service: ProductService,
		private util: ToolsService,
	) {
	}

	ngOnInit() {
		if (this.data.id > 0) {
			this.filters.produto_id = this.data.id;
		}
		this.load_list();
	}

	_loading(type = false) {
		this.loading = type;
		this.ref.detectChanges();
	}

	close(params = undefined) {
		this.modalActive.close(params);
	}

	load_list() {
		this._loading();
		this.service.getVariations(this.filters).subscribe(resp => {
			console.log(resp);
		}, error => {

		})
	}

	open_variation(item = {}) {

	}

	// getDados(id) {
	// 	this.loading = true;
	// 	this.service.getVariation(id).subscribe(resp => {
	// 		this.loading = false;
	// 		this.dados = resp;
	// 		this.ref.detectChanges();
	// 	}, erro => {
	// 		this.loading = false;
	// 		this.ref.detectChanges();
	// 	});
	// }

	// submit() {
	// 	if (this.dados.id) {
	// 		this.update();
	// 	} else {
	// 		this.create();
	// 	}
	// }

	// create() {
	// 	this.loading = true;
	// 	this.service.createVariation(this.dados).subscribe(resp => {
	// 		this.loading = false;
	// 		this.close(resp);
	// 		this.ref.detectChanges();
	// 	}, erro => {
	// 		this.loading = false;
	// 		this.ref.detectChanges();
	// 	});
	// }

	// update() {
	// 	this.loading = true;
	// 	this.service.updateVariation(this.dados, this.dados.id).subscribe(resp => {
	// 		this.loading = false;
	// 		this.close(resp);
	// 		this.ref.detectChanges();
	// 	}, erro => {
	// 		this.loading = false;
	// 		this.ref.detectChanges();
	// 	});
	// }

}

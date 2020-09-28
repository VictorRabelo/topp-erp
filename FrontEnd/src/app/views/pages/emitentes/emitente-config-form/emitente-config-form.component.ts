import { Component, OnInit, Input, ChangeDetectorRef } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { EmitenteService } from '../../../../services/emitente.service';
import { ToolsService } from '../../../../services/tools.service';

@Component({
	selector: 'kt-emitente-config-form',
	templateUrl: './emitente-config-form.component.html',
	styleUrls: ['./emitente-config-form.component.scss']
})
export class EmitenteConfigFormComponent implements OnInit {

	loading = false;

	config: any = {};
	screen: number;

	@Input() data;

	constructor(
		private ref: ChangeDetectorRef,
		private service: EmitenteService,
		private util: ToolsService,
		private modalActive: NgbActiveModal
	) {
		this.screen = util.getScreen();
	}

	ngOnInit() {
		console.log(this.data.modelo)
		if (this.data.modelo.id) {
			this.config = this.data.modelo;
			this.getDados(this.config.id);
		} else {
			this.config = this.data.modelo;
			this.config.emitente_id = this.data.emitente_id;
		}
	}

	close(params = undefined) {
		this.modalActive.close(params);
	}

	getDados(id) {
		this.loading = true;
		this.service.getByIdConfig(id).subscribe(resp => {
			this.loading = false;
			this.config = resp;
			this.ref.detectChanges();
		}, erro => {
			this.loading = false;
			this.ref.detectChanges();
		});
	}

	submit() {
		if (this.config.id) {
			this.update();
		} else {
			this.create();
		}
	}

	create() {
		this.loading = true;
		this.service.createConfig(this.config).subscribe(resp => {
			this.loading = false;
			this.close(resp);
			this.ref.detectChanges();
		}, erro => {
			this.loading = false;
			this.ref.detectChanges();
		});
	}

	update() {
		this.loading = true;
		this.service.updateConfig(this.config, this.config.id).subscribe(resp => {
			this.loading = false;
			this.close(resp);
			this.ref.detectChanges();
		}, erro => {
			this.loading = false;
			this.ref.detectChanges();
		});
	}

}

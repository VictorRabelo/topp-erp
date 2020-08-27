import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Store, select } from '@ngrx/store';
import { AppState } from '../../../core/reducers';
import { currentUser } from '../../../core/auth';

import { MessageService } from '../../../services/message.service';
import { FiscalService } from '../../../services/fiscal.service';
import { EmitenteService } from '../../../services/emitente.service';


@Component({
	selector: 'kt-manifesto',
	templateUrl: './manifesto.component.html',
	styleUrls: ['./manifesto.component.scss']
})
export class ManifestoComponent implements OnInit {

	filters: any = {};
	permissions: any = {};
	pageCurrent = 1;

	emitentes = [];
	dataSource = [];
	loading: boolean = false;

	constructor(
		private ref: ChangeDetectorRef,
		private message: MessageService,
		private service: FiscalService,
		private serviceEmitente: EmitenteService,
		private modalCtrl: NgbModal,
		private store: Store<AppState>,
	) {
		this.getEmitentes();
	}

	ngOnInit() {
		// this.load_list();
		this.getPermissions();
	}

	_loading(type = false) {
		this.loading = type;
		this.ref.detectChanges();
	}


	getPermissions() {
		this.store.pipe(select(currentUser)).subscribe((resp: any) => {
			if (resp) {
				console.log(resp.permissions);
				this.permissions = resp.permissions;
			}
		});
	}
	getEmitentes() {
		this.serviceEmitente.getList({}).subscribe(resp => {
			this.emitentes = resp;
			if (this.emitentes.length == 1) {
				this.filters.emitente_id = this.emitentes[0].id;
				this.setEmitente();
				this.load_list();
			}
		});
	}

	load_list() {
		this.dataSource = [];

		this._loading(true);
		this.service.getListaNotas(this.filters).subscribe(resp => {
			this.dataSource = resp;
			this._loading();
		}, erro => {
			this._loading();
		});
	}

	search_notas() {
		this.dataSource = [];

		this._loading(true);
		this.service.getMonitorSefaz(this.filters).subscribe((resp: any) => {
			this.message.toastSuccess(`Foram encontrados ${resp.registros} novos documentos!`);
			this._loading();

			this.load_list();
		}, erro => {
			this._loading();
			this.load_list();
		});
	}

	setEmitente() {
		this.emitentes.forEach(e => {
			if (e.id == this.filters.emitente_id) {
				this.filters.emitente = e.razao;
			}
		})
	}

	manifestar(evento, item) {

		const request = {
			'emitente_id': this.filters.emitente_id,
			'tpevento': evento,
			'xjust': ''
		};

		if (evento != "210240") {
			this.message.swal.fire({
				// title: 'Manifestação ?',
				icon: 'question',
				html: `Desaja manifestar esta nota ?`,
				confirmButtonText: 'Confirmar',
				showCancelButton: true,
				cancelButtonText: 'Não',
			}).then((result) => {
				if (!result.dismiss) {
					this.sendManifesta(request, item);
				}
			});
		} else {
			this.message.swal.fire({
				// title: 'Manifestação ?',
				icon: 'question',
				html: `Informe o motivo!`,
				input: 'textarea',
				confirmButtonText: 'Confirmar',
				showCancelButton: true,
				cancelButtonText: 'Voltar',
			}).then((result) => {
				if (result.value != "") {
					request.xjust = result.value;
					this.sendManifesta(request, item);
				}
			});
		}

	}

	sendManifesta(request, item) {
		this._loading(true);
		this.service.manifestaNFe(request, item.id).subscribe(resp => {
			this.load_list();
			this._loading();
		}, erro => {
			this.message.alertErro(erro.error.text)
			this._loading();
		});
	}

	verificaOptions(event, item) {
		let achou = false;
		if (item.eventos) {
			item.eventos.forEach((e: any) => {
				if (e.tpevento == event) {
					achou = true;
				}
			});
		}

		return achou;
	}

	// open_emitentes() {
	// 	const modalRef = this.modalCtrl.open(GeraNotaComponent, { size: 'sm', backdrop: 'static' });
	// 	modalRef.componentInstance.data = {};
	// 	modalRef.result.then(res => {
	// 		if (res) {
	// 			this.filters.emitente_id = res.emitente_id;
	// 		}
	// 	})
	// }

	open_filters(content) {
		this.modalCtrl.open(content, { size: 'sm', backdrop: 'static' });
	}

}

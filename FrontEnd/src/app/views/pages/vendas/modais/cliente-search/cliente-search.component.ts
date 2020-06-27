import { Component, OnInit, ChangeDetectorRef, Input } from '@angular/core';
import { NgbActiveModal, NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ToolsService } from '../../../../../services/tools.service';
import { ClientService } from '../../../../../services/client.service';
import { VendaService } from '../../../../../services/venda.service';
import { ClienteFormComponent } from '../../../clientes/cliente-form/cliente-form.component';

@Component({
   selector: 'kt-cliente-search',
   templateUrl: './cliente-search.component.html',
   styleUrls: ['./cliente-search.component.scss']
})
export class ClienteSearchComponent implements OnInit {

   loading = false;

   source: any = {};
   dataSource = [];
   screen: number;

   filters: any = {};

   @Input() data;

   constructor(
      private ref: ChangeDetectorRef,
      private service: ClientService,
      private serviceVenda: VendaService,
      private util: ToolsService,
      private modalActive: NgbActiveModal,
      private modalCtrl: NgbModal
   ) {
      this.screen = this.util.getScreen();

   }

   ngOnInit() {
      this.source = this.data;
      this.list();
   }

   close(params = undefined) {
      this.modalActive.close(params);
   }

   list() {
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
      this.close(item);
   }

   cadastro_cliente() {
      const modalRef = this.modalCtrl.open(ClienteFormComponent, { size: 'lg', backdrop: 'static' });
      modalRef.result.then(resp => {
         if (resp) {
            this.list();
         }
      })
   }

}

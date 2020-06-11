import { Component, OnInit, ChangeDetectorRef, Input } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { ToolsService } from '../../../../../services/tools.service';
import { ClientService } from '../../../../../services/client.service';
import { VendaService } from '../../../../../services/venda.service';

@Component({
   selector: 'kt-cliente-search',
   templateUrl: './cliente-search.component.html',
   styleUrls: ['./cliente-search.component.scss']
})
export class ClienteSearchComponent implements OnInit {

   loading = false;

   vendaCurrent: any = {};
   dataSource = [];
   screen: number;

   filters: any = {};

   @Input() data;

   constructor(
      private ref: ChangeDetectorRef,
      private service: ClientService,
      private serviceVenda: VendaService,
      private util: ToolsService,
      private modalActive: NgbActiveModal
   ) {
      this.screen = this.util.getScreen();

   }

   ngOnInit() {
      this.vendaCurrent = this.data;
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

      const request = {
         'cliente_id': item.id,
         'cliente': item.razao,
         'cpf': item.cnpj,
      }

      this.loading = true;
      this.serviceVenda.setClient(request, this.vendaCurrent.id).subscribe(resp => {
         this.close(resp);
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      })
   }

}

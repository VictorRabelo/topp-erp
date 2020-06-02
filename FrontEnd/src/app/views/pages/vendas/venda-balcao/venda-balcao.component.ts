import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { MessageService } from '../../../../services/message.service';
import { VendaService } from '../../../../services/venda.service';
import { ToolsService } from '../../../../services/tools.service';

@Component({
   selector: 'kt-venda-balcao',
   templateUrl: './venda-balcao.component.html',
   styleUrls: ['./venda-balcao.component.scss']
})
export class VendaBalcaoComponent implements OnInit {

   loading: boolean = false;

   vendaCurrent: any = {}
   cliente: any = {};
   vendedor: any = {};
   ItemSource = [];

   constructor(
      private ref: ChangeDetectorRef,
      private message: MessageService,
      private service: VendaService,
      private util: ToolsService,
      private ActiveRoute: ActivatedRoute,
      private router: Router
   ) { }

   ngOnInit() {
      this.loading = true;
      this.ActiveRoute.params.subscribe(params => {
         if (params['id']) {
            const id = params['id'];
            this.getDados(id)
         }
         else {
            this.router.navigate(['/vendas']);
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
      this.loading = true;
      this.service.getById(id).subscribe((resp: any) => {
         this.loading = false;

         this.vendaCurrent = resp;

         this.ref.detectChanges();
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

}

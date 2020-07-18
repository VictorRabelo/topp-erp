import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { FinanceiroService } from '../../../../services/financeiro.service';
import { MessageService } from '../../../../services/message.service';
import { ToolsService } from '../../../../services/tools.service';

@Component({
   selector: 'kt-caixa-resumo',
   templateUrl: './caixa-resumo.component.html',
   styleUrls: ['./caixa-resumo.component.scss']
})
export class CaixaResumoComponent implements OnInit {

   resumo = [];
   loading: boolean = false;


   constructor(
      private ref: ChangeDetectorRef,
      private message: MessageService,
      private service: FinanceiroService,
      private util: ToolsService,
      private modalCtrl: NgbModal,
   ) {
      this.getResumo();
   }

   ngOnInit() {
   }

   getResumo() {
      this.loading = true;
      this.service.caixaResumo({}).subscribe((resp: any) => {
         this.loading = false;

         this.resumo = resp;

         this.ref.detectChanges();
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   filters() { }

}

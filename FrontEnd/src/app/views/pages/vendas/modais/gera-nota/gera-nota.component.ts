import { Component, OnInit, ChangeDetectorRef, Input } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from '../../../../../services/message.service';
import { ToolsService } from '../../../../../services/tools.service';
import { FiscalService } from '../../../../../services/fiscal.service';
import { EmitenteService } from '../../../../../services/emitente.service';

@Component({
   selector: 'kt-gera-nota',
   templateUrl: './gera-nota.component.html',
   styleUrls: ['./gera-nota.component.scss']
})
export class GeraNotaComponent implements OnInit {

   dados: any = {};

   emitentes = [];

   venda: any = {};

   loading: boolean = false;
   screen: number;

   @Input() data;

   constructor(
      private ref: ChangeDetectorRef,
      private service: FiscalService,
      private serviceEmitente: EmitenteService,
      private util: ToolsService,
      private message: MessageService,
      private modalActive: NgbActiveModal,
   ) {
      this.screen = this.util.getScreen();
      this.loadEmitentes();
   }

   ngOnInit() {
      this.venda = this.data.venda;
      this.dados.modelo = this.data.modelo;
   }

   close(params = undefined) {
      this.modalActive.close(params);
   }


   loadEmitentes() {
      this.loading = true;
      this.serviceEmitente.getList({}).subscribe(resp => {
         this.emitentes = resp;
         this.loading = false;
         this.ref.detectChanges();
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   gera_nota() {
      if (this.dados.modelo == 55) {

         this.close(this.dados);

      } else if (this.dados.modelo == 65) {
         this.dados.venda_id = this.venda.id;
         this.gerar_nfce();
      }
   }

   gerar_nfce() {
      this.loading = true;
      this.service.createNFCe(this.dados).subscribe((resp: any) => {
         console.log(resp);
         if (resp.pdf_url) {
            window.open(resp.pdf_url, '_blank');
         }
         this.loading = false;
         this.ref.detectChanges();
         this.close(true);
      }, erro => {
         this.message.alertErro(erro.error.text);
         this.loading = false;
         this.ref.detectChanges();
      });
   }
}

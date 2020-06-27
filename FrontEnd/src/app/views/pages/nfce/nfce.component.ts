import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { Router } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from '../../../services/message.service';
import { NFCeService } from '../../../services/nfce.service';
import { FiscalService } from '../../../services/fiscal.service';

@Component({
   selector: 'kt-nfce',
   templateUrl: './nfce.component.html',
   styleUrls: ['./nfce.component.scss']
})
export class NfceComponent implements OnInit {

   loading = false;

   dataSource = [];

   constructor(
      private ref: ChangeDetectorRef,
      private message: MessageService,
      private service: NFCeService,
      private serviceFiscal: FiscalService,
      private modalCtrl: NgbModal,
      private router: Router
   ) {
      this.load_list();
   }

   ngOnInit() {
   }

   load_list() {
      this.loading = true;
      this.service.getList({}).subscribe(resp => {
         this.loading = false;
         this.dataSource = resp;
         this.ref.detectChanges();
      }, erro => {
         this.ref.detectChanges();
         console.log(erro);
         this.loading = false;
      });
   }

   cancel_item(item) {
      this.message.swal.fire({
         title: `Cancelar NFCe: ${item.numero}`,
         input: 'textarea',
         inputPlaceholder: 'Informe uma justificativa para o cancelamento...',
         showCancelButton: true,
         confirmButtonText: 'Confirmar',
         cancelButtonText: 'Voltar'
      }).then(resp => {
         console.log(resp);
         if (resp.value) {
            item.xjust = resp.value;
            this.cancela_nfc(item);
         }
      });
   }

   cancela_nfc(nota) {
      this.loading = true;
      this.ref.detectChanges();
      this.serviceFiscal.cancelNFCe(nota, nota.id).subscribe((resp: any) => {
         this.loading = false;
         this.ref.detectChanges();
         this.load_list()
      }, erro => {
         this.message.alertErro(erro.error.text);
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   print_item(item) {
      this.loading = true;
      this.ref.detectChanges();
      this.serviceFiscal.printNFCe(item).subscribe((resp: any) => {
         if (resp.pdf_url) {
            window.open(resp.pdf_url, '_blank');
         }
         this.loading = false;
         this.ref.detectChanges();
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   filters() { }

}

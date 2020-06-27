import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { Router } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from '../../../services/message.service';
import { NFeService } from '../../../services/nfe.service';
import { FiscalService } from '../../../services/fiscal.service';

@Component({
   selector: 'kt-nfe',
   templateUrl: './nfe.component.html',
   styleUrls: ['./nfe.component.scss']
})
export class NfeComponent implements OnInit {

   loading = false;

   dataSource = [];

   constructor(
      private ref: ChangeDetectorRef,
      private message: MessageService,
      private service: NFeService,
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

   add() {

      this.message.swal.fire({
         title: 'Criar nova NFe ?',
         icon: 'question',
         confirmButtonText: 'Sim',
         cancelButtonText: 'Não',
         showCancelButton: true
      }).then(result => {
         if (result.value) {

            this.loading = true;
            this.service.create({}).subscribe((resp: any) => {
               this.loading = false;
               this.ref.detectChanges();
               this.open_nota(resp.id);
            }, erro => {
               this.ref.detectChanges();
               console.log(erro);
               this.loading = false;
            });

         }
      });
   }

   open_nota(id) {
      this.router.navigate([`nfe/${id}`]);
   }

   cancel_item(item) {
      this.message.swal.fire({
         title: `Cancelar NFe: ${item.numero}`,
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
      this.serviceFiscal.cancelNFe(nota, nota.id).subscribe((resp: any) => {
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
      this.serviceFiscal.printNFe(item).subscribe((resp: any) => {
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

import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { EmitenteService } from '../../../services/emitente.service';
import { MessageService } from '../../../services/message.service';
import { EmitenteFormComponent } from './emitente-form/emitente-form.component';
import { Router } from '@angular/router';

@Component({
   selector: 'kt-emitentes',
   templateUrl: './emitentes.component.html',
   styleUrls: ['./emitentes.component.scss']
})
export class EmitentesComponent implements OnInit {

   loading = false;

   dataSource = [];

   constructor(
      private ref: ChangeDetectorRef,
      private message: MessageService,
      private service: EmitenteService,
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
      const modalRef = this.modalCtrl.open(EmitenteFormComponent, { size: 'lg', backdrop: 'static' });

      modalRef.result.then(resp => {
         if (resp != undefined) {
            this.load_list();
         }
      });
   }

   edit(item) {
      // const modalRef = this.modalCtrl.open(EmitenteFormComponent, { size: 'lg', backdrop: 'static' });
      // modalRef.componentInstance.data = item;
      // modalRef.result.then(resp => {
      //    if (resp != undefined) {
      //       this.load_list();
      //    }
      // });
      this.router.navigate([`/emitente/${item.id}`]);
   }

   delete_item(item) {
      this.message.swal.fire({
         title: 'Excluir ?',
         icon: 'question',
         html: `Desaja excluir o emitente: <br><b>${item.razao}</b> ?`,
         confirmButtonText: 'Confirmar',
         showCancelButton: true,
         cancelButtonText: 'Não',
      }).then((result) => {
         if (!result.dismiss) {
            this.delete(item);
            // console.log(result);
         }
      });
   }

   delete(item) {
      this.loading = true;
      this.service.delete(item.id).subscribe(resp => {
         this.loading = false;
         this.load_list();
      }, erro => {
         this.loading = false;
      });
   }

}

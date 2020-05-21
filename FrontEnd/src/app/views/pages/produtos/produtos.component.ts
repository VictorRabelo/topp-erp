import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { MessageService } from '../../../services/message.service';
import { ProductService } from '../../../services/product.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ProdutoFormComponent } from './produto-form/produto-form.component';

@Component({
   selector: 'kt-produtos',
   templateUrl: './produtos.component.html',
   styleUrls: ['./produtos.component.scss']
})
export class ProdutosComponent implements OnInit {

   loading = false;

   dataSource = [];

   constructor(
      private ref: ChangeDetectorRef,
      private message: MessageService,
      private service: ProductService,
      private modalCtrl: NgbModal

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
      const modalRef = this.modalCtrl.open(ProdutoFormComponent, { size: 'lg', backdrop: 'static' });

      modalRef.result.then(resp => {
         if (resp != undefined) {
            this.load_list();
         }
      });
   }

   edit(item) {
      const modalRef = this.modalCtrl.open(ProdutoFormComponent, { size: 'lg', backdrop: 'static' });
      modalRef.componentInstance.data = item;
      modalRef.result.then(resp => {
         if (resp != undefined) {
            this.load_list();
         }
      });

   }

   delete_item(item) {
      this.message.swal.fire({
         title: 'Excluir ?',
         icon: 'question',
         html: `Desaja excluir o cadastro: <br><b>${item.descricao}</b> ?`,
         confirmButtonText: 'Confirmar',
         showCancelButton: true,
         cancelButtonText: 'Não',
         showLoaderOnConfirm: true,
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
         this.ref.detectChanges();
         this.load_list();
      }, erro => {
         this.ref.detectChanges();
         this.loading = false;
      });
   }

}

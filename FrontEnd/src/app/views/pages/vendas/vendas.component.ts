import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
// import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Router } from '@angular/router';
import { Store, select } from '@ngrx/store';
import { MessageService } from '../../../services/message.service';
import { VendaService } from '../../../services/venda.service';
import { ToolsService } from '../../../services/tools.service';
import { AppState } from '../../../core/reducers';
import { currentUser } from '../../../core/auth';

@Component({
   selector: 'kt-vendas',
   templateUrl: './vendas.component.html',
   styleUrls: ['./vendas.component.scss']
})
export class VendasComponent implements OnInit {

   loading = false;

   permissions: any = {};

   dataSource = [];
   screen: number;

   constructor(
      private ref: ChangeDetectorRef,
      private message: MessageService,
      private service: VendaService,
      private util: ToolsService,
      private store: Store<AppState>,
      private router: Router,
   ) {
      this.screen = this.util.getScreen();
      this.load_list();

      this.getPermissions();
   }

   ngOnInit() {
   }

   getPermissions() {
      this.store.pipe(select(currentUser)).subscribe((resp: any) => {
         if (resp) {
            console.log(resp.permissions);
            this.permissions = resp.permissions;
         }
      });
   }

   load_list() {
      this.loading = true;
      this.service.getList({}).subscribe(resp => {
         this.loading = false;
         this.dataSource = resp;
         this.ref.detectChanges();
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   add() {
      this.loading = true;
      this.service.create({}).subscribe((resp: any) => {
         this.loading = false;

         this.open(resp.dados);

         this.ref.detectChanges();
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   open(venda) {
      this.router.navigate(['/venda_standart', venda.id]);
   }

   delete_confirm(item) {
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
         }
      });
   }

   delete(item) {
      this.loading = true;
      this.service.delete(item.id).subscribe(resp => {
         this.loading = false;
         this.ref.detectChanges();
         this.load_list();
      }, () => {
         this.ref.detectChanges();
         this.loading = false;
      });
   }

}

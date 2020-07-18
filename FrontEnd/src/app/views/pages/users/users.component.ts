import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { Router } from '@angular/router';
import { Store, select } from '@ngrx/store';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from '../../../services/message.service';
import { UserService } from '../../../services/user.service';
import { ToolsService } from '../../../services/tools.service';
import { PermissionsFormComponent } from './permissions-form/permissions-form.component';
import { UserFormComponent } from './user-form/user-form.component';
import { AppState } from '../../../core/reducers';
import { currentUser } from '../../../core/auth';

@Component({
   selector: 'kt-users',
   templateUrl: './users.component.html',
   styleUrls: ['./users.component.scss']
})
export class UsersComponent implements OnInit {

   loading = false;

   dataSource = [];
   screen: number;

   filters: any = {};
   permissions: any = {};

   constructor(
      private ref: ChangeDetectorRef,
      private message: MessageService,
      private service: UserService,
      private util: ToolsService,
      private router: Router,
      private store: Store<AppState>,
      private modalCtrl: NgbModal
   ) {
      this.screen = util.getScreen();
      this.load_list();
   }

   ngOnInit() {
      this.getPermissions();
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
      this.service.getList(this.filters).subscribe(resp => {
         this.loading = false;
         this.dataSource = resp;
         this.ref.detectChanges();
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   open_nivel(item = {}) {
      const modalRef = this.modalCtrl.open(PermissionsFormComponent, { size: 'lg', backdrop: 'static' });
      modalRef.componentInstance.data = item;
      modalRef.result.then(resp => {
         if (resp != undefined) {
            // this.load_list();
            location.reload();
         }
      })
   }

   open(item = {}) {
      const modalRef = this.modalCtrl.open(UserFormComponent, { size: 'md', backdrop: 'static' });
      modalRef.componentInstance.data = item;
      modalRef.result.then(resp => {
         if (resp != undefined) {
            this.load_list();
         }
      })
      // this.router.navigate(['/venda_standart', venda.id]);
   }

   delete_confirm(item) {
      this.message.swal.fire({
         title: 'Excluir ?',
         icon: 'question',
         html: `Desaja excluir o cadastro: <br><b>${item.nome}</b> ?`,
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

   open_filters(content) {
      this.modalCtrl.open(content, { size: 'md', backdrop: 'static' });
   }

}

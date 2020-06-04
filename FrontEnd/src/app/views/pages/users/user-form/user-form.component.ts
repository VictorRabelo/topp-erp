import { Component, OnInit, ChangeDetectorRef, Input } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { ToolsService } from '../../../..//services/tools.service';
import { UserService } from '../../../..//services/user.service';
import { NgForm } from '@angular/forms';

@Component({
   selector: 'kt-user-form',
   templateUrl: './user-form.component.html',
   styleUrls: ['./user-form.component.scss']
})
export class UserFormComponent implements OnInit {

   loading = false;

   permissionsList = [];
   user: any = {};
   screen: number;

   @Input() data;

   constructor(
      private ref: ChangeDetectorRef,
      private service: UserService,
      private util: ToolsService,
      private modalActive: NgbActiveModal
   ) {
      this.screen = util.getScreen();
      this.listPermissions();
   }

   ngOnInit() {
      this.user = this.data;
      if (this.user.id) {
         this.getUser(this.user.id);
      }
   }

   close(params = undefined) {
      this.modalActive.close(params);
   }
   listPermissions() {
      this.service.getListPermissions().subscribe(resp => {
         this.permissionsList = resp;
      });
   }

   getUser(id) {
      this.loading = true;
      this.service.getById(id).subscribe(resp => {
         this.loading = false;
         this.user = resp;
         this.ref.detectChanges();
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   submit(form: NgForm) {
      if (!form.valid) {
         console.log(form);
         return;
      }

      if (this.user.id) {
         this.update();
      } else {
         this.create();
      }
   }

   create() {
      this.loading = true;
      this.service.create(this.user).subscribe(resp => {
         this.loading = false;
         this.close(resp);
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }
   update() {
      this.loading = true;
      this.service.update(this.user, this.user.id).subscribe(resp => {
         this.loading = false;
         this.close(resp);
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   alterPassword() {

   }

}

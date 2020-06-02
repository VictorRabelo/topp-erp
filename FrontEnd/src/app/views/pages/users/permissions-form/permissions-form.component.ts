import { Component, OnInit, ChangeDetectorRef, Input } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { Router } from '@angular/router';
import { MessageService } from '../../../../services/message.service';
import { UserService } from '../../../../services/user.service';
import { ToolsService } from '../../../../services/tools.service';


@Component({
   selector: 'kt-permissions-form',
   templateUrl: './permissions-form.component.html',
   styleUrls: ['./permissions-form.component.scss']
})
export class PermissionsFormComponent implements OnInit {

   loading = false;

   user: any = {};
   permissions: any = {};
   screen: number;

   @Input() data;

   constructor(
      private ref: ChangeDetectorRef,
      private message: MessageService,
      private service: UserService,
      private util: ToolsService,
      private router: Router,
      private modalActive: NgbActiveModal
   ) {
      this.screen = util.getScreen();
   }

   ngOnInit() {
      this.user = this.data;
      if (this.user.permissions) {
         this.getPermissions(this.user.permissions);
      }
   }

   close(params = undefined) {
      this.modalActive.close(params);
   }

   getPermissions(id_prmission) {
      this.loading = true;
      this.service.getByIdPermissions(id_prmission).subscribe(resp => {
         this.loading = false;
         this.permissions = resp;
         this.ref.detectChanges();
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   submit() {
      if (this.permissions.id) {
         this.update();
      } else {
         this.create();
      }
   }

   create() {
      this.loading = true;
      this.service.createPermissions(this.permissions).subscribe(resp => {
         this.loading = false;
         this.permissions = resp;
         this.ref.detectChanges();
      }, erro => {
         this.loading = false;
         this.ref.detectChanges();
      });
   }

   update() {

   }
}

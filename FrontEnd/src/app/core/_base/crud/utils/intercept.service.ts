// Angular
import { Injectable } from '@angular/core';
import { HttpEvent, HttpInterceptor, HttpHandler, HttpRequest, HttpResponse } from '@angular/common/http';
// RxJS
import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';
import { environment } from '../../../../../environments/environment';
import { MessageService } from '../../../../services/message.service';
import { Router } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

/**
 * More information there => https://medium.com/@MetonymyQT/angular-http-interceptors-what-are-they-and-how-to-use-them-52e060321088
 */
@Injectable()
export class InterceptService implements HttpInterceptor {

   constructor(
      private message: MessageService,
      private redirect: Router,
      private modalCtrl: NgbModal
   ) { }

   // intercept request and add token
   intercept(
      request: HttpRequest<any>,
      next: HttpHandler
   ): Observable<HttpEvent<any>> {
      // tslint:disable-next-line:no-debugger
      // modify request
      request = request.clone({
         setHeaders: {
            Authorization: `Bearer ${localStorage.getItem(environment.authTokenKey)}`
         }
      });
      // console.log('----request----');
      // console.log(request);
      // console.log('--- end of request---');

      return next.handle(request).pipe(
         tap(
            event => {
               if (event instanceof HttpResponse) {
                  // console.log(event);
                  if (event.status == 201) {
                     this.message.toastSuccess(event.body.message, '');
                  }
               }
            },
            error => {
               if (error.status == 0) {
                  this.message.toastError('Parece que você não tem internet!', 'Falha na conexão');
               } else if (error.status == 401) {
                  this.message.toastError('Faça login novamente para continuar', 'Sessão expirada');

                  localStorage.removeItem(environment.authTokenKey);
                  this.modalCtrl.dismissAll();
                  this.redirect.navigate(['/auth']);

               } else {
                  this.message.toastError(error.error.message, 'Falha na requisição');
               }

               console.error(error);
               // console.error(error.message);
            }
         )
      );
   }
}

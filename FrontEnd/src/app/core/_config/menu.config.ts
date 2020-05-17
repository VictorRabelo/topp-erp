import { Observable } from 'rxjs';
import { Store } from '@ngrx/store';
import { AppState } from '../reducers';

export class MenuConfig {


   constructor(private store: Store<AppState>) {

   }

   public defaults: any = {
      header: {
         self: {},
         items: [
            {
               title: 'Dashboards',
               root: true,
               alignment: 'left',
               page: '/dashboard',
               // translate: 'MENU.DASHBOARD',
            }
         ]
      },

      aside: {
         self: {},
         items: [
            {
               title: 'Dashboard',
               root: true,
               icon: 'flaticon2-architecture-and-city',
               page: '/dashboard',
               // translate: 'MENU.DASHBOARD',
               bullet: 'dot',
            },
            // { section: 'Components' },
            {
               title: 'Cadastros',
               root: true,
               icon: 'flaticon2-browser-2',
               submenu: [
                  {
                     title: 'Clientes',
                     icon: 'fas fa-users',
                     page: '/clientes'
                  },
                  {
                     title: 'Produtos',
                     icon: 'fas fa-box-open',
                     page: '/produtos'
                  }
               ]
            },
         ]
      },
   };

   public get configs(): any {
      return this.defaults;
   }

   getPermissions(item) {
      return new Observable<boolean>(observer => {
         observer.next(true);
      });
   }
}

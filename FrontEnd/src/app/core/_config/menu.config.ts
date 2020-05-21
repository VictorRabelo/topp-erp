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
            },
            {
               title: 'Cadastros',
               root: true,
               // icon: 'flaticon2-browser-2',
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
            {
               title: 'Estoque',
               root: true,
               // icon: 'flaticon2-browser-2',
               submenu: [
                  {
                     title: 'Movimentar Estoque',
                     icon: 'fas fa-dolly',
                     page: '/estoque_mov'
                  }
               ]
            },
            {
               title: 'Vendas',
               root: true,
               // icon: 'flaticon2-browser-2',
               submenu: [
                  {
                     title: 'Balcão',
                     icon: 'fas fa-shopping-basket',
                     page: '/venda_standart'
                  },
                  {
                     title: 'PDV - Frente de Caixa',
                     icon: 'fas fa-desktop',
                     page: '/venda_front'
                  },
                  {
                     title: 'Lista de Vendas',
                     icon: 'fas fa-cash-register',
                     page: '/vendas'
                  }
               ]
            },
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

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
                  },
                  {
                     title: 'Usuários',
                     icon: 'fas fa-user',
                     page: '/users'
                  },

               ]
            },
            {
               title: 'Vendas',
               root: true,
               // icon: 'flaticon2-browser-2',
               submenu: [
                  {
                     title: 'Orçamentos',
                     icon: 'fas fa-clipboard',
                     // page: '/venda_standart'
                     page: '/orcamentos'
                  },
                  {
                     title: 'PDV - Frente de Caixa',
                     icon: 'fas fa-desktop',
                     page: '/venda_front'
                  },
                  {
                     title: 'Vendas',
                     icon: 'fas fa-shopping-basket',
                     page: '/vendas'
                  }
               ]
            },
            {
               title: 'Fiscal',
               root: true,
               // icon: 'flaticon2-browser-2',
               submenu: [
                  {
                     title: 'NFe',
                     icon: 'fas fa-file-alt',
                     page: '/nfe'
                  },
                  {
                     title: 'NFCe',
                     icon: 'fas fa-receipt',
                     page: '/nfce'
                  },
                  {
                     title: 'Emitentes',
                     icon: 'fas fa-building',
                     page: '/emitentes'
                  }
               ]
            },
            {
               title: 'Financeiro',
               root: true,
               // icon: 'flaticon2-browser-2',
               submenu: [
                  {
                     title: 'Caixa',
                     icon: 'fas fa-cash-register',
                     page: '/caixa'
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

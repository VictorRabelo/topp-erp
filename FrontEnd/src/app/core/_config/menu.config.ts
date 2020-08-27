import { Observable } from 'rxjs';
import { Store, select } from '@ngrx/store';
import { AppState } from '../reducers';
import { currentUser } from '../auth';

export class MenuConfig {


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
							page: '/clientes',
							show$: this.getPermissions('clients')
						},
						{
							title: 'Produtos',
							icon: 'fas fa-box-open',
							page: '/produtos',
							show$: this.getPermissions('products')
						},
						{
							title: 'Usuários',
							icon: 'fas fa-user',
							page: '/users',
							show$: this.getPermissions('users')
						},

					]
				},
				{
					title: 'Vendas',
					root: true,
					// icon: 'flaticon2-browser-2',
					submenu: [
						// {
						//    title: 'Orçamentos',
						//    icon: 'fas fa-clipboard',
						//    // page: '/venda_standart'
						//    page: '/orcamentos'
						// },
						// {
						//    title: 'PDV - Frente de Caixa',
						//    icon: 'fas fa-desktop',
						//    page: '/venda_front'
						// },
						{
							title: 'Vendas',
							icon: 'fas fa-shopping-basket',
							page: '/vendas',
							show$: this.getPermissions('vendas')
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
							page: '/nfe',
							show$: this.getPermissions('nfe')
						},
						{
							title: 'NFCe',
							icon: 'fas fa-receipt',
							page: '/nfce',
							show$: this.getPermissions('nfce')
						},
						{
							title: 'Monitor Fiscal',
							icon: 'fas fa-tv',
							page: '/monitor-fiscal',
							show$: this.getPermissions('monitor')
						},
						{
							title: 'Emitentes',
							icon: 'fas fa-building',
							page: '/emitentes',
							show$: this.getPermissions('emitentes')
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
							page: '/caixa',
							show$: this.getPermissions('caixa')
						},
						{
							title: 'Contas a Pagar',
							icon: 'fas fa-hand-holding-usd',
							page: '/contas_pagar',
							show$: this.getPermissions('contas_pagar')
						},
						{
							title: 'Contas a Receber',
							icon: 'fas fa-hand-holding-usd',
							page: '/contas_receber',
							show$: this.getPermissions('contas_receber')
						},
					]
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

	constructor(private store: Store<AppState>) {

	}

	getPermissions(nivel) {
		return new Observable<boolean>(observer => {
			observer.next(false);
			this.store.pipe(select(currentUser))
				.subscribe(res => {
					if (res) {
						if (nivel == "clients") {
							observer.next(res.permissions.clients);
						}
						if (nivel == "products") {
							observer.next(res.permissions.products);
						}
						if (nivel == "users") {
							observer.next(res.permissions.users);
						}
						if (nivel == "vendas") {
							observer.next(res.permissions.vendas);
						}
						if (nivel == "nfe") {
							observer.next(res.permissions.nfe);
						}
						if (nivel == "nfce") {
							observer.next(res.permissions.nfce);
						}
						if (nivel == "emitentes") {
							observer.next(res.permissions.emitentes);
						}
						if (nivel == "caixa") {
							observer.next(res.permissions.caixa);
						}
						if (nivel == "contas_pagar") {
							observer.next(res.permissions.contas_pagar);
						}
						if (nivel == "contas_receber") {
							observer.next(res.permissions.contas_receber);
						}
						if (nivel == "monitor") {
							observer.next(true);
						}
					}
				});
		})
	}
}

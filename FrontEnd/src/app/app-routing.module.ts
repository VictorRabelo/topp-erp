// Angular
import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
// Components
import { BaseComponent } from './views/theme/base/base.component';
import { ErrorPageComponent } from './views/theme/content/error-page/error-page.component';
// Auth
import { AuthGuard } from './core/auth';

const routes: Routes = [
	{ path: 'auth', loadChildren: () => import('./views/pages/auth/auth.module').then(m => m.AuthModule) },
	{ path: 'venda_front', loadChildren: () => import('./views/pages/vendas/venda-front/venda-front.module').then(m => m.VendaFrontModule) },
	{ path: 'store/:id', loadChildren: () => import('./views/pages/store/store.module').then(m => m.StoreModule) },

	{
		path: '',
		component: BaseComponent,
		canActivate: [AuthGuard],
		children: [
			{ path: '', redirectTo: 'dashboard', pathMatch: 'full' },
			{
				path: 'dashboard',
				loadChildren: () => import('./views/pages/dashboard/dashboard.module').then(m => m.DashboardModule),
			},
			{
				path: 'theme',
				loadChildren: () => import('./views/theme/content/builder/builder.module').then(m => m.BuilderModule),
			},
			{
				path: '',
				loadChildren: () => import('./views/pages/pages.module').then(m => m.PagesModule),
			},
			{
				path: 'error/403',
				component: ErrorPageComponent,
				data: {
					type: 'error-v6',
					code: 403,
					title: '403... Access forbidden',
					desc: 'Looks like you don\'t have permission to access for requested page.<br> Please, contact administrator',
				},
			},
			{ path: 'error/:type', component: ErrorPageComponent },
			{ path: '**', redirectTo: 'dashboard', pathMatch: 'full' },
		],
	},

	{ path: '**', redirectTo: 'error/403', pathMatch: 'full' },
];

@NgModule({
	imports: [
		RouterModule.forRoot(routes),
	],
	exports: [RouterModule],
})
export class AppRoutingModule {
}

import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { StoreComponent } from './store.component';
import { RouterModule, Routes } from '@angular/router';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';

const rotas: Routes = [
	{ path: '', component: StoreComponent }
]

@NgModule({
	declarations: [StoreComponent],
	imports: [
		CommonModule,
		RouterModule.forChild(rotas),
		NgbModule
	]
})
export class StoreModule { }

import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Routes, RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';


import { VendaFrontComponent } from './venda-front.component';

const routes: Routes = [
   {
      path: '', children: [
         { path: '', component: VendaFrontComponent }
      ]
   }
];

@NgModule({
   declarations: [VendaFrontComponent],
   imports: [
      CommonModule,
      RouterModule.forChild(routes),
      FormsModule,
   ]
})
export class VendaFrontModule { }

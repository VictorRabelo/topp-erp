import { Injectable } from "@angular/core";
import { HttpClient, HttpBackend } from '@angular/common/http';

import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';

@Injectable({
	providedIn: 'root'
})
export class FinanceiroService {
	public url = environment.url;

	constructor(
		public http: HttpClient
	) { }

	caixaResumo(queryParams: any): Observable<any[]> {
		return this.http.get<any[]>(`${this.url}/financeiro/caixa`, { params: queryParams });
	}

	//payments Forms
	getListPayments(queryParams: any): Observable<any[]> {
		return this.http.get<any[]>(`${this.url}/financeiro/payments`, { params: queryParams });
	}
	createPayment(data: any): Observable<any[]> {
		return this.http.post<any[]>(`${this.url}/financeiro/createPayment`, data);
	}
	updatePayment(data: any, id: number): Observable<any[]> {
		return this.http.put<any[]>(`${this.url}/financeiro/updatePayment/${id}`, data);
	}
	getByIdPayment(id: number): Observable<any[]> {
		return this.http.get<any[]>(`${this.url}/financeiro/showPayment/${id}`);
	}
	deletePayments(id: number): Observable<any[]> {
		return this.http.delete<any[]>(`${this.url}/financeiro/payments/${id}`);
	}

	//contas receber
	getListContasPagar(queryParams: any): Observable<any[]> {
		return this.http.get<any[]>(`${this.url}/financeiro/contas-pagar`, { params: queryParams });
	}
	getContasPagar(id: number): Observable<any[]> {
		return this.http.get<any[]>(`${this.url}/financeiro/contas-pagar/${id}`);
	}
	createContaPagar(data: any): Observable<any[]> {
		return this.http.post<any[]>(`${this.url}/financeiro/contas-pagar`, data);
	}
	updateContaPagar(data: any, id: number): Observable<any[]> {
		return this.http.put<any[]>(`${this.url}/financeiro/contas-pagar/${id}`, data);
	}
	deleteContasPagar(id: number): Observable<any[]> {
		return this.http.delete<any[]>(`${this.url}/financeiro/contas-pagar/${id}`);
	}
	paymentContaPagar(data: any): Observable<any[]> {
		return this.http.post<any[]>(`${this.url}/financeiro/contas-pagar/paymentConta`, data);
	}

	//contas receber
	getListContasReceber(queryParams: any): Observable<any[]> {
		return this.http.get<any[]>(`${this.url}/financeiro/contas-receber`, { params: queryParams });
	}
	getContasReceber(id: number): Observable<any[]> {
		return this.http.get<any[]>(`${this.url}/financeiro/contas-receber/${id}`);
	}
	createContaReceber(data: any): Observable<any[]> {
		return this.http.post<any[]>(`${this.url}/financeiro/contas-receber`, data);
	}
	updateContaReceber(data: any, id: number): Observable<any[]> {
		return this.http.put<any[]>(`${this.url}/financeiro/contas-receber/${id}`, data);
	}
	deleteContasReceber(id: number): Observable<any[]> {
		return this.http.delete<any[]>(`${this.url}/financeiro/contas-receber/${id}`);
	}
	paymentContaReceber(data: any): Observable<any[]> {
		return this.http.post<any[]>(`${this.url}/financeiro/contas-receber/paymentConta`, data);
	}
}

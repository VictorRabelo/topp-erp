import { Component, OnInit } from '@angular/core';

@Component({
	selector: 'kt-store',
	templateUrl: './store.component.html',
	styleUrls: ['./store.component.scss']
})
export class StoreComponent implements OnInit {
	public isCollapsed = true;

	constructor() { }

	ngOnInit() {
	}

	collapseToggle() {
		this.isCollapsed = !this.isCollapsed
	}

}

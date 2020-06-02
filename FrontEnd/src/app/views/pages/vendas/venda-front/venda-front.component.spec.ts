import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { VendaFrontComponent } from './venda-front.component';

describe('VendaFrontComponent', () => {
  let component: VendaFrontComponent;
  let fixture: ComponentFixture<VendaFrontComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ VendaFrontComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(VendaFrontComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

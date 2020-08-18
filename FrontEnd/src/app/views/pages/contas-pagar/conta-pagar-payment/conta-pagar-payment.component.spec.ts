import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContaPagarPaymentComponent } from './conta-pagar-payment.component';

describe('ContaPagarPaymentComponent', () => {
  let component: ContaPagarPaymentComponent;
  let fixture: ComponentFixture<ContaPagarPaymentComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContaPagarPaymentComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContaPagarPaymentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

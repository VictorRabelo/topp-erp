import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContaReceberPaymentComponent } from './conta-receber-payment.component';

describe('ContaReceberPaymentComponent', () => {
  let component: ContaReceberPaymentComponent;
  let fixture: ComponentFixture<ContaReceberPaymentComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContaReceberPaymentComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContaReceberPaymentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

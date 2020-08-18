import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FormsPaymentsComponent } from './forms-payments.component';

describe('FormsPaymentsComponent', () => {
  let component: FormsPaymentsComponent;
  let fixture: ComponentFixture<FormsPaymentsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FormsPaymentsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FormsPaymentsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

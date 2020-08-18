import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContaPagarFormComponent } from './conta-pagar-form.component';

describe('ContaPagarFormComponent', () => {
  let component: ContaPagarFormComponent;
  let fixture: ComponentFixture<ContaPagarFormComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContaPagarFormComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContaPagarFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

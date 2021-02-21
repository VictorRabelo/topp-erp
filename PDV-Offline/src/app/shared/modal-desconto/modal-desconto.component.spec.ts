import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ModalDescontoComponent } from './modal-desconto.component';

describe('ModalDescontoComponent', () => {
  let component: ModalDescontoComponent;
  let fixture: ComponentFixture<ModalDescontoComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ModalDescontoComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ModalDescontoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

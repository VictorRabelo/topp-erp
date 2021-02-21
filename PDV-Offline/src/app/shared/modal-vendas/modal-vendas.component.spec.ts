import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ModalVendasComponent } from './modal-vendas.component';

describe('ModalVendasComponent', () => {
  let component: ModalVendasComponent;
  let fixture: ComponentFixture<ModalVendasComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ModalVendasComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ModalVendasComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

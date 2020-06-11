import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { EmitenteDetalheComponent } from './emitente-detalhe.component';

describe('EmitenteDetalheComponent', () => {
  let component: EmitenteDetalheComponent;
  let fixture: ComponentFixture<EmitenteDetalheComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ EmitenteDetalheComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EmitenteDetalheComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { CaixaResumoComponent } from './caixa-resumo.component';

describe('CaixaResumoComponent', () => {
  let component: CaixaResumoComponent;
  let fixture: ComponentFixture<CaixaResumoComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ CaixaResumoComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CaixaResumoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

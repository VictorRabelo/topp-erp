import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ProdutoMovStockComponent } from './produto-mov-stock.component';

describe('ProdutoMovStockComponent', () => {
  let component: ProdutoMovStockComponent;
  let fixture: ComponentFixture<ProdutoMovStockComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ProdutoMovStockComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ProdutoMovStockComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

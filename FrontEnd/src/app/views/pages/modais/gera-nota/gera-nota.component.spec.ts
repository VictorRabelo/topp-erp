import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { GeraNotaComponent } from './gera-nota.component';

describe('GeraNotaComponent', () => {
  let component: GeraNotaComponent;
  let fixture: ComponentFixture<GeraNotaComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ GeraNotaComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(GeraNotaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

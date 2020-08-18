import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContaReceberFormComponent } from './conta-receber-form.component';

describe('ContaReceberFormComponent', () => {
  let component: ContaReceberFormComponent;
  let fixture: ComponentFixture<ContaReceberFormComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContaReceberFormComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContaReceberFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

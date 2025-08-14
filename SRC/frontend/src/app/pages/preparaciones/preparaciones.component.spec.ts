import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PreparacionesComponent } from './preparaciones.component';

describe('PreparacionesComponent', () => {
  let component: PreparacionesComponent;
  let fixture: ComponentFixture<PreparacionesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PreparacionesComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(PreparacionesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

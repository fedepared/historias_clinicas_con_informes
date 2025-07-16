import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EditReportesComponent } from './edit-reportes.component';

describe('EditReportesComponent', () => {
  let component: EditReportesComponent;
  let fixture: ComponentFixture<EditReportesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [EditReportesComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(EditReportesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

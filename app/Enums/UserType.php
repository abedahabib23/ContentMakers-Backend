<?php

namespace App\Enums;

enum UserType: string
{
    case SuperAdmin = 'super_admin';
    case Trainer = 'trainer';
    case Trainee = 'trainee';
    case Guest = 'guest';
    case TrainingCoordinator = 'training_coordinator';
}

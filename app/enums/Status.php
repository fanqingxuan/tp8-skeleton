<?php

namespace app\enums;

enum Status:int {
    case Wait = 1;
    case Doing = 2;
    case Finish = 3;
}
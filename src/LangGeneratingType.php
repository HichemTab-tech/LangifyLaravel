<?php

namespace HichemtabTech\LangifyLaravel;

enum LangGeneratingType: int
{
    case COMPLETE_THE_MISSING = 0;
    case FORCE_OVERWRITE = 1;
}

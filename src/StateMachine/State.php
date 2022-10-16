<?php

namespace Letov\HtmlParser\StateMachine;

enum State
{
    case TEXT;
    case TAG_OPEN_BEGIN;
    case TAG_OPEN_NAME;
    case TAG_OPEN_SINGLE;
    case TAG_OPEN_INSIDE;
    case ATTR;
    case ATTR_NAME;
    case ATTR_VALUE;
    case TAG_CLOSE_NAME;
    case DONE;
}

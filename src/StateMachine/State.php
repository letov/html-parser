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
    case ATTR_VALUE_NO_QUOTES;
    case ATTR_VALUE_QUOTES;
    case TAG_CLOSE_NAME;
    case DONE;
}

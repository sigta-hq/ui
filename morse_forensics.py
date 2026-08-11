#!/usr/bin/env python3
"""
Morse forensic re-reader.

Takes the ORIGINAL (mangled) morse, throws away its unreliable letter-spaces,
and re-derives the letter-breaks ("flips the spaces") by walking a target token
and matching each base32 letter's morse group against the raw element stream.

It auto-detects injected junk elements by re-syncing, and prints an exact list
of every mutation: break-shifts (spacing), inserted junk, and content swaps.
"""
import base64

# ---- morse tables ---------------------------------------------------------
MORSE = {'.-':'A','-...':'B','-.-.':'C','-..':'D','.':'E','..-.':'F','--.':'G',
'....':'H','..':'I','.---':'J','-.-':'K','.-..':'L','--':'M','-.':'N','---':'O',
'.--.':'P','--.-':'Q','.-.':'R','...':'S','-':'T','..-':'U','...-':'V','.--':'W',
'-..-':'X','-.--':'Y','--..':'Z','-----':'0','.----':'1','..---':'2','...--':'3',
'....-':'4','.....':'5','-....':'6','--...':'7','---..':'8','----.':'9'}
INV = {v: k for k, v in MORSE.items()}

def to_morse(b32):            # base32 string -> spaced morse
    return ' '.join(INV[c] for c in b32)
def stream(b32):              # base32 string -> raw element stream (no gaps)
    return ''.join(INV[c] for c in b32)
def b32decode(s):
    return base64.b32decode(s + '=' * ((-len(s)) % 8))

# ---- inputs ---------------------------------------------------------------
ORIGINAL = "-.- .-. -..- .-- -.... ...-- .-.. -... -.- -.. ... .-- ..- --.- -.. .-.. .-.. --- .--- ... -.- -. ....- ....- ....-"
HINT  = "KRXW63LBNZ4XGZLRWYDN4HBA"          # what re-spacing alone can recover
TRUE  = "KRXW63LBNZ4XGZLDOJSXI4ZB"          # base32("Toomanysecrets!") — the real flag

def is_subseq(sub, s):
    it = iter(s); return all(c in it for c in sub)

# ---- the re-spacer / aligner ---------------------------------------------
def resegment(raw, target_b32, max_skip=8):
    """Walk target letters, place a break after each letter's morse group.
    Classify each step: KEEP (pure re-space), JUNK (skipped inserted elements),
    or SUBST (raw content differs from what the target needs)."""
    log, pos = [], 0
    for c in target_b32:
        grp = INV[c]; L = len(grp)
        if raw[pos:pos+L] == grp:                      # break just lands here
            log.append(('KEEP', c, grp, pos)); pos += L
            continue
        skip = next((s for s in range(1, max_skip+1)   # inserted junk before it?
                     if raw[pos+s:pos+s+L] == grp), None)
        if skip is not None:
            log.append(('JUNK', raw[pos:pos+skip], pos)); pos += skip
            log.append(('KEEP', c, grp, pos)); pos += L
        else:                                          # content was altered
            log.append(('SUBST', c, raw[pos:pos+L], grp, pos)); pos += L
    return log, raw[pos:]

def report(name, target):
    raw = ''.join(ORIGINAL.split())
    print(f"\n{'='*72}\nTARGET: {name} = {target}")
    print(f"reachable by re-spacing alone (subsequence of raw stream)? {is_subseq(stream(target), raw)}")
    log, tail = resegment(raw, target)
    keeps  = sum(1 for e in log if e[0]=='KEEP')
    junks  = [e for e in log if e[0]=='JUNK']
    substs = [e for e in log if e[0]=='SUBST']
    for e in log:
        if e[0]=='KEEP':
            print(f"  keep   letter {e[1]}  {e[2]:<6} @{e[3]}")
        elif e[0]=='JUNK':
            print(f"  JUNK   removed {e[1]!r} injected @{e[2]}")
        else:
            print(f"  SWAP   letter {e[1]} needs {e[3]:<6} but stream has {e[2]:<6} @{e[4]}")
    if tail: print(f"  trailing leftover elements: {tail!r}")
    print(f"  -> {keeps} letters recovered by re-spacing, "
          f"{len(junks)} junk splice(s), {len(substs)} content swap(s)")
    return log

# ---- run ------------------------------------------------------------------
raw = ''.join(ORIGINAL.split())
print("ORIGINAL morse   :", ORIGINAL)
print("naive decode     :", ''.join(MORSE[t] for t in ORIGINAL.split()),
      "  (len", len(ORIGINAL.split()), "-> invalid base32)")
print("raw element stream:", raw, f"({len(raw)} elements)")

report("HINT (space+junk recoverable)", HINT)
report("TRUE flag", TRUE)

print(f"\n{'='*72}\nCORRECTED morse (true flag):\n ", to_morse(TRUE))
print("decodes to base32:", TRUE)
print("base32 -> text   :", b32decode(TRUE).decode())

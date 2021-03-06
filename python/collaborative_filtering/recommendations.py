critics={'Lisa Rose': {'Lady in the Water': 2.5, 'Snakes on a Plane': 3.5,
 'Just My Luck': 3.0, 'Superman Returns': 3.5, 'You, Me and Dupree': 2.5, 
 'The Night Listener': 3.0},
'Gene Seymour': {'Lady in the Water': 3.0, 'Snakes on a Plane': 3.5, 
 'Just My Luck': 1.5, 'Superman Returns': 5.0, 'The Night Listener': 3.0, 
 'You, Me and Dupree': 3.5}, 
'Michael Phillips': {'Lady in the Water': 2.5, 'Snakes on a Plane': 3.0,
 'Superman Returns': 3.5, 'The Night Listener': 4.0},
'Claudia Puig': {'Snakes on a Plane': 3.5, 'Just My Luck': 3.0,
 'The Night Listener': 4.5, 'Superman Returns': 4.0, 
 'You, Me and Dupree': 2.5},
'Mick LaSalle': {'Lady in the Water': 3.0, 'Snakes on a Plane': 4.0, 
 'Just My Luck': 2.0, 'Superman Returns': 3.0, 'The Night Listener': 3.0,
 'You, Me and Dupree': 2.0}, 
'Jack Matthews': {'Lady in the Water': 3.0, 'Snakes on a Plane': 4.0,
 'The Night Listener': 3.0, 'Superman Returns': 5.0, 'You, Me and Dupree': 3.5},
'Toby': {'Snakes on a Plane':4.5,'You, Me and Dupree':1.0,'Superman Returns':4.0}}

from math import sqrt

# Euclid distance
def sim_distance(prefs, person1, person2):
  si = {}
  for item in prefs[person1]: 
    if item in prefs[person2]: 
      si[item] = 1

  if len(si) == 0:
    return 0

  sum_of_squares = sum([pow(prefs[person1][item] - prefs[person2][item], 2) for item in prefs[person1] if item in prefs[person2]])
  return 1/(1 + sum_of_squares)

# Pearson correlation
def sim_pearson(prefs, p1, p2):
  si = {}
  for item in prefs[p1]: 
    if item in prefs[p2]: 
      si[item] = 1

  if len(si) == 0: 
    return 0

  n = len(si)
  
  sum1 = sum([prefs[p1][it] for it in si])
  sum2 = sum([prefs[p2][it] for it in si])
  
  sum1Sq = sum([pow(prefs[p1][it], 2) for it in si])
  sum2Sq = sum([pow(prefs[p2][it], 2) for it in si])	
  
  pSum = sum([prefs[p1][it] * prefs[p2][it] for it in si])
  
  num = pSum - (sum1 * sum2/n)
  den = sqrt((sum1Sq - pow(sum1, 2)/n) * (sum2Sq - pow(sum2, 2)/n))
  if den == 0:
    return 0

  r = num/den
  return r

# Tanimoto coefficient
def sim_tanimoto(prefs, p1, p2):
  c = {}
  for item in prefs[p1]: 
    if item in prefs[p2]: 
      if prefs[p1][item] == prefs[p2][item]:
        c[item] = 1;
  return float(len(c))/(len(prefs[p1]) + len(prefs[p2]) - len(c))

# Jaccard index
def sim_jaccard(prefs, p1, p2):
  c = []
  b = []
  d = []
  f = []
  for item in prefs[p1]:
    c.append(prefs[p1][item])

  for item in prefs[p2]:
    b.append(prefs[p2][item])

  d = [val for val in c if val in b]
  f = c + b
  return float(len(d)) / float(len(f))

#print sim_distance(critics, 'Lisa Rose', 'Gene Seymour'), '\n'
#print sim_pearson(critics, 'Lisa Rose', 'Gene Seymour'), '\n'
#print sim_tanimoto(critics, 'Lisa Rose', 'Gene Seymour'), '\n'
#print sim_jaccard(critics, 'Lisa Rose', 'Gene Seymour'), '\n'

# Returns the best matches for person from the prefs dictionary.
# Number of results and similarity function are optional params.
def topMatches(prefs, person, n=5, similarity = sim_pearson):
  scores = [(similarity(prefs, person, other), other)
                  for other in prefs if other!=person]
  scores.sort()
  scores.reverse()
  return scores[0:n]

#print topMatches(critics, 'Toby', n=3), '\n'

# Gets recommendations for a person by using a weighted average
# of every other user's rankings
def getRecommendations(prefs,person,similarity=sim_pearson):
  totals={}
  simSums={}
  for other in prefs:
    # don't compare me to myself
    if other==person: continue
    sim=similarity(prefs,person,other)

    # ignore scores of zero or lower
    if sim<=0: continue
    for item in prefs[other]:

      # only score movies I haven't seen yet
      if item not in prefs[person] or prefs[person][item]==0:
        # Similarity * Score
        totals.setdefault(item,0)
        totals[item]+=prefs[other][item]*sim
        # Sum of similarities
        simSums.setdefault(item,0)
        simSums[item]+=sim

  # Create the normalized list
  rankings=[(total/simSums[item],item) for item,total in totals.items()]

  # Return the sorted list
  rankings.sort()
  rankings.reverse()
  return rankings

#print getRecommendations(critics, 'Toby')

def transformPrefs(prefs):
  result={}
  for person in prefs:
    for item in prefs[person]:
      result.setdefault(item,{})
      
      # Flip item and person
      result[item][person]=prefs[person][item]
  return result

#print transformPrefs(critics)

movies = transformPrefs(critics)
#print topMatches(movies, 'Superman Returns')
#print getRecommendations(movies, 'Just My Luck')





